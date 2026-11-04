<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeOrderTypeRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}
    public function index() { return OrderResource::collection(Order::with('items.menu', 'payment')->latest()->paginate()); }
    public function store(StoreOrderRequest $request): OrderResource { return new OrderResource($this->orders->create($request->validated(), $request->user()?->id)); }
    public function show(Order $order): OrderResource { return new OrderResource($order->load('items.menu', 'payment')); }
    public function changeType(ChangeOrderTypeRequest $request, Order $order): OrderResource { return new OrderResource($this->orders->changeType($order, $request->validated('order_type'), $request->user()?->id)); }
}
