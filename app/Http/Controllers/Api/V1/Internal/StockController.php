<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStockRequest;
use App\Services\ActivityLogger;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;

class StockController extends Controller
{
    public function index(StockService $stocks): JsonResponse
    {
        return response()->json(['data' => $stocks->todayRows()]);
    }

    public function update(UpdateStockRequest $request, StockService $stocks, ActivityLogger $logger): JsonResponse
    {
        $changes = $stocks->updateToday($request->validated('items'));

        if ($changes) {
            $logger->log($request->user()->role, 'update_stock', null, ['items' => $changes]);
        }

        return response()->json(['data' => $stocks->todayRows()]);
    }
}