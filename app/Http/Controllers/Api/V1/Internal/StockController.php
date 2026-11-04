<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockRowResource;
use App\Models\DailyStock;

class StockController extends Controller
{
    public function index() { return StockRowResource::collection(DailyStock::with('menu')->whereDate('stock_date', today())->get()); }
    public function update(UpdateStockRequest $request): StockRowResource { $stock = DailyStock::updateOrCreate(['menu_id' => $request->validated('menu_id'), 'stock_date' => $request->validated('stock_date')], ['quantity' => $request->validated('quantity')]); return new StockRowResource($stock->load('menu')); }
}
