<?php

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Payment */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'gross_amount' => $this->gross_amount,
            'qr_string' => $this->qr_string,
            'qr_url' => $this->qr_url,
            'expires_at' => $this->expires_at?->toIso8601String(),
        ];
    }
}