<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScanRequest;
use App\Http\Resources\StaffOrderResource;
use App\Models\Order;

class ScanController extends Controller
{
    public function __invoke(ScanRequest $request): StaffOrderResource { return new StaffOrderResource(Order::with('items.menu', 'payment')->where('order_number', $request->validated('order_number'))->firstOrFail()); }
}
