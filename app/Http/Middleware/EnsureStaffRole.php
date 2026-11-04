<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStaffRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $role = $request->user()?->staffAccess?->role?->value;
        abort_unless($role && in_array($role, $roles, true), 403, 'Akses staf tidak diizinkan.');
        return $next($request);
    }
}
