<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index() { return ActivityLogResource::collection(ActivityLog::with('user')->latest()->paginate()); }
}
