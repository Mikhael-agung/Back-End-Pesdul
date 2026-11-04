<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Resources\KitchenCardResource;
use App\Models\Order;
use App\Enums\OrderStatus;

class KitchenController extends Controller
{
    public function index() { return KitchenCardResource::collection(Order::with('items.menu')->whereIn('status', [OrderStatus::Paid, OrderStatus::Processing])->latest()->get()); }
    public function ready(Order $order): KitchenCardResource { $order->update(['status' => OrderStatus::Ready]); return new KitchenCardResource($order->refresh()); }
}
