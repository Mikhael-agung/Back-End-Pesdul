<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartValidateRequest;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function validateCart(CartValidateRequest $request): JsonResponse
    {
        $total = collect($request->validated('items'))->sum(fn ($item) => Menu::findOrFail($item['menu_id'])->price * $item['quantity']);
        return response()->json(['data' => ['subtotal' => $total, 'total' => $total]]);
    }
}
