<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function showDineInFull(): JsonResponse
    {
        return response()->json(['data' => ['enabled' => Setting::dineInFull()]]);
    }

    public function updateDineInFull(Request $request, ActivityLogger $logger): JsonResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $enabled = (bool) $data['enabled'];

        $changed = Setting::dineInFull() !== $enabled;
        Setting::putValue('dine_in_full', $enabled ? '1' : '0');

        if ($changed) {
            $logger->log($request->user()->role, 'update_dine_in_full', null, ['enabled' => $enabled]);
        }

        return response()->json(['data' => ['enabled' => $enabled]]);
    }
}