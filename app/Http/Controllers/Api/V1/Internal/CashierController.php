<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffOrderResource;
use App\Models\Order;
use App\Enums\OrderStatus;

class CashierController extends Controller
{
    public function index() { return StaffOrderResource::collection(Order::with('items.menu')->where('status', OrderStatus::Paid)->latest()->paginate()); }
    public function complete(Order $order): StaffOrderResource { $order->update(['status' => OrderStatus::Completed]); return new StaffOrderResource($order->refresh()); }
}
