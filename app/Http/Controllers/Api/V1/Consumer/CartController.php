<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartValidateRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public function check(CartValidateRequest $request, CartService $cart): JsonResponse
    {
        $result = $cart->evaluate($request->validated('items'));

        return response()->json([
            'data' => [
                'valid' => $result['valid'],
                'total' => $result['total'],
                'items' => array_map(
                    fn (array $line) => Arr::only($line, ['menu_id', 'unit_price', 'qty', 'available', 'stock_remaining']),
                    $result['lines'],
                ),
            ],
        ]);
    }
}