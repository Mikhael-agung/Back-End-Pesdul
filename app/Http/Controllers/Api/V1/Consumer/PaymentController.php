<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function create(Order $order, MidtransService $midtrans): JsonResponse { return response()->json(['data' => $midtrans->createPayment($order)]); }
}
