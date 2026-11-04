<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function log(string $action, ?Model $subject = null, array $metadata = []): ActivityLog
    {
        return ActivityLog::create(['user_id' => auth()->id(), 'action' => $action, 'subject_type' => $subject?->getMorphClass(), 'subject_id' => $subject?->getKey(), 'metadata' => $metadata]);
    }
}
