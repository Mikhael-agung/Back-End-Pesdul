<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Order;
use App\Enums\OrderStatus;

class ReportController extends Controller
{
    public function sales(ReportRequest $request) { $query = Order::where('status', OrderStatus::Completed); if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->validated('from')); if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->validated('to')); return response()->json(['data' => ['orders' => $query->count(), 'revenue' => $query->sum('total')]]); }
}
