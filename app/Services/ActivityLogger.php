<?php

namespace App\Services;

use App\Enums\StaffRole;
use App\Models\ActivityLog;

class ActivityLogger
{
    public function log(StaffRole $role, string $action, ?string $target = null, ?array $detail = null): ActivityLog
    {
        return ActivityLog::create([
            'role' => $role->value,
            'action' => $action,
            'target' => $target,
            'detail' => $detail,
        ]);
    }
}