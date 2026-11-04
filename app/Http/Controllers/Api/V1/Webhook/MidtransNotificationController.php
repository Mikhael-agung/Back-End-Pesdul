<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans): JsonResponse { $midtrans->applyNotification($request->all()); return response()->json(['message' => 'ok']); }
}
