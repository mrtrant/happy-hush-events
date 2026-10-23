<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $module = $request->input('module');

        $permissions = DB::table('permissions')
            ->select('permissions.*')
            ->selectSub(
                DB::table('role_permissions')
                    ->whereColumn(
                        'role_permissions.permission_id',
                        'permissions.id'
                    )
                    ->selectRaw('COUNT(*)'),
                'role_count'
            )
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where(
                                'permissions.permission_key',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'permissions.description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $module,
                fn ($query) => $query->where(
                    'permissions.module',
                    $module
                )
            )
            ->orderBy('permissions.module')
            ->orderBy('permissions.permission_key')
            ->paginate(30)
            ->withQueryString();

        $modules = DB::table('permissions')
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.permissions.index', compact(
            'permissions',
            'modules',
            'search',
            'module'
        ));
    }
}
