<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        abort_unless($user, 401);

        /*
         * The Administrator currently manages the entire system.
         * Admin therefore bypasses individual permission checks.
         */
        $isAdmin = DB::table('user_roles')
            ->join(
                'roles',
                'roles.id',
                '=',
                'user_roles.role_id'
            )
            ->where('user_roles.user_id', $user->id)
            ->where('roles.name', 'Admin')
            ->where('roles.is_active', true)
            ->exists();

        if ($isAdmin) {
            return $next($request);
        }

        /*
         * Future non-admin users must have the requested
         * permission through one of their active roles.
         */
        $hasPermission = DB::table('user_roles')
            ->join(
                'roles',
                'roles.id',
                '=',
                'user_roles.role_id'
            )
            ->join(
                'role_permissions',
                'role_permissions.role_id',
                '=',
                'roles.id'
            )
            ->join(
                'permissions',
                'permissions.id',
                '=',
                'role_permissions.permission_id'
            )
            ->where('user_roles.user_id', $user->id)
            ->where('roles.is_active', true)
            ->where(
                'permissions.permission_key',
                $permission
            )
            ->exists();

        abort_unless(
            $hasPermission,
            403,
            'You do not have permission to perform this action.'
        );

        return $next($request);
    }
}
