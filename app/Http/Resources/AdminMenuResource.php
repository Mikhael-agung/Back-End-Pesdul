<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AdminMenuResource extends MenuResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request) + [
            'is_active' => $this->is_active,
            'has_photo' => $this->photo !== null,
        ];
    }
}