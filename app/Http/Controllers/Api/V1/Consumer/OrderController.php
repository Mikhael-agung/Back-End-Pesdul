<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Enums\OrderType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderSummaryResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const RELATIONS = ['items.menu', 'typeLogs', 'payment'];

    public function store(StoreOrderRequest $request, OrderService $orders): JsonResponse
    {
        $order = $orders->create(
            $request->user(),
            OrderType::from($request->validated('order_type')),
            $request->validated('items'),
        );

        return (new OrderResource($order->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $page = $request->user()->orders()->latest('id')->paginate($data['per_page'] ?? 10);

        return response()->json([
            'data' => OrderSummaryResource::collection($page->items()),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $code): OrderResource
    {
        $order = $request->user()->orders()
            ->where('code', $code)
            ->with(self::RELATIONS)
            ->firstOrFail();

        return new OrderResource($order);
    }
}