<?php // app/Http/Resources/ActivityLogResource.php

namespace App\Http\Resources;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ActivityLog */
class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role->value,
            'action' => $this->action,
            'target' => $this->target,
            'detail' => $this->detail,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}