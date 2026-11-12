<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use RuntimeException;
use Throwable;

/** Satu-satunya class yang menyentuh SDK Midtrans. Mengembalikan array ternormalisasi, bukan objek SDK. */
class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
        ];
    }

    /** @return array{transaction_id: ?string, qr_string: ?string, qr_url: ?string} */
    public function chargeQris(string $gatewayOrderId, int $amount, CarbonInterface $expiresAt): array
    {
        $minutes = max(1, (int) ceil(($expiresAt->getTimestamp() - now()->getTimestamp()) / 60));

        try {
            $response = $this->toArray(CoreApi::charge([
                'payment_type' => 'qris',
                'transaction_details' => ['order_id' => $gatewayOrderId, 'gross_amount' => $amount],
                'qris' => ['acquirer' => config('midtrans.acquirer')],
                'custom_expiry' => [
                    'order_time' => now()->format('Y-m-d H:i:s O'),
                    'expiry_duration' => $minutes,
                    'unit' => 'minute',
                ],
            ]));
        } catch (Throwable $e) {
            throw $this->gatewayError($e);
        }

        $qrUrl = collect($response['actions'] ?? [])->firstWhere('name', 'generate-qr-code')['url'] ?? null;
        $qrString = $response['qr_string'] ?? null;

        if ($qrUrl === null && $qrString === null) {
            Log::error('Midtrans: respons charge QRIS tanpa QR', [
                'order_id' => $gatewayOrderId,
                'status_code' => $response['status_code'] ?? null,
                'status_message' => $response['status_message'] ?? null,
            ]);

            throw new BusinessException('Gagal membuat tagihan QRIS.', 'PAYMENT_GATEWAY_ERROR', 502);
        }

        return [
            'transaction_id' => $response['transaction_id'] ?? null,
            'qr_string' => $qrString,
            'qr_url' => $qrUrl,
        ];
    }

    /**
     * Status terkini di gateway. Transaksi yang tidak ada di Midtrans (404) dianggap 'failed'.
     *
     * @return array{transaction_id: ?string, status: string}
     */
    public function status(string $gatewayOrderId): array
    {
        try {
            $response = $this->toArray(Transaction::status($gatewayOrderId));
        } catch (Throwable $e) {
            if ((int) $e->getCode() === 404) {
                return ['transaction_id' => null, 'status' => 'failed'];
            }

            throw $this->gatewayError($e);
        }

        if (! isset($response['transaction_status'])) {
            if ((int) ($response['status_code'] ?? 0) === 404) {
                return ['transaction_id' => null, 'status' => 'failed'];
            }

            throw $this->gatewayError(new RuntimeException((string) ($response['status_message'] ?? 'Respons status kosong.')));
        }

        return [
            'transaction_id' => $response['transaction_id'] ?? null,
            'status' => $this->mapStatus((string) $response['transaction_status'], $response['fraud_status'] ?? null),
        ];
    }

    /** Upaya terbaik: gagal membatalkan (misal sudah settlement) tidak melempar error. */
    public function cancel(string $gatewayOrderId): void
    {
        try {
            Transaction::cancel($gatewayOrderId);
        } catch (Throwable $e) {
            Log::warning('Midtrans: cancel gagal', ['order_id' => $gatewayOrderId, 'message' => $e->getMessage()]);
        }
    }

    /** SHA512(order_id + status_code + gross_amount + server key). */
    public function isValidSignature(array $payload): bool
    {
        $signature = (string) ($payload['signature_key'] ?? '');

        if ($signature === '') {
            return false;
        }

        $expected = hash('sha512',
            (string) ($payload['order_id'] ?? '')
            .(string) ($payload['status_code'] ?? '')
            .(string) ($payload['gross_amount'] ?? '')
            .(string) config('midtrans.server_key'));

        return hash_equals($expected, $signature);
    }

    /** Status gateway ke status payments: paid | pending | failed | expired | ignored (refund dsb, tidak diproses). */
    public function mapStatus(string $transactionStatus, ?string $fraudStatus = null): string
    {
        if ($fraudStatus === 'deny') {
            return 'failed';
        }

        return match ($transactionStatus) {
            'settlement' => 'paid',
            'capture' => in_array($fraudStatus, [null, 'accept'], true) ? 'paid' : 'pending',
            'pending' => 'pending',
            'deny', 'cancel', 'failure' => 'failed',
            'expire' => 'expired',
            default => 'ignored',
        };
    }

    private function toArray(mixed $result): array
    {
        return json_decode((string) json_encode($result), true) ?? [];
    }

    private function gatewayError(Throwable $e): BusinessException
    {
        Log::error('Midtrans error', ['message' => $e->getMessage(), 'code' => $e->getCode()]);

        return new BusinessException('Payment gateway tidak dapat dihubungi. Coba lagi sebentar.', 'PAYMENT_GATEWAY_ERROR', 502);
    }
}