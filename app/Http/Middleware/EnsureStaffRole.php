<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Models\StaffAccess;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pemakaian: role:consumer  |  role:kasir,admin  |  role:kasir,dapur,admin
 */
class EnsureStaffRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $actor = $request->user();

        $allowed = match (true) {
            $actor instanceof User => in_array('consumer', $roles, true),
            $actor instanceof StaffAccess => in_array($actor->role->value, $roles, true),
            default => false,
        };

        if (! $allowed) {
            throw new BusinessException('Akses ditolak untuk role ini.', 'ROLE_FORBIDDEN', 403);
        }

        return $next($request);
    }
}