<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\Admin\RoleManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleManagementService $roleService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $roles = DB::table('roles')
            ->select('roles.*')
            ->selectSub(
                DB::table('user_roles')
                    ->whereColumn(
                        'user_roles.role_id',
                        'roles.id'
                    )
                    ->selectRaw('COUNT(*)'),
                'user_count'
            )
            ->selectSub(
                DB::table('role_permissions')
                    ->whereColumn(
                        'role_permissions.role_id',
                        'roles.id'
                    )
                    ->selectRaw('COUNT(*)'),
                'permission_count'
            )
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where(
                                'roles.name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'roles.description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $status === 'active',
                fn ($query) => $query->where(
                    'roles.is_active',
                    true
                )
            )
            ->when(
                $status === 'inactive',
                fn ($query) => $query->where(
                    'roles.is_active',
                    false
                )
            )
            ->orderBy('roles.name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.roles.index', compact(
            'roles',
            'search',
            'status'
        ));
    }

    public function create(): View
    {
        $permissions = $this->permissions();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            $roleId = $this->roleService->create(
                $request->validated()
            );

            if ($request->filled('permissions')) {
                $this->roleService->syncPermissions(
                    $roleId,
                    $request->validated('permissions')
                );
            }

            return redirect()
                ->route('admin.roles.show', $roleId)
                ->with('success', 'Role created successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create the role.'
                );
        }
    }

    public function show(int $role): View
    {
        $roleRecord = DB::table('roles')
            ->where('id', $role)
            ->first();

        abort_unless($roleRecord, 404);

        $permissions = DB::table('permissions')
            ->join(
                'role_permissions',
                'role_permissions.permission_id',
                '=',
                'permissions.id'
            )
            ->where('role_permissions.role_id', $role)
            ->orderBy('permissions.module')
            ->orderBy('permissions.permission_key')
            ->select('permissions.*')
            ->get();

        $users = DB::table('users')
            ->join(
                'user_roles',
                'user_roles.user_id',
                '=',
                'users.id'
            )
            ->where('user_roles.role_id', $role)
            ->whereNull('users.deleted_at')
            ->select([
                'users.id',
                'users.user_code',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.is_active',
            ])
            ->orderBy('users.first_name')
            ->get();

        return view('admin.roles.show', compact(
            'roleRecord',
            'permissions',
            'users'
        ));
    }

    public function edit(int $role): View
    {
        $roleRecord = DB::table('roles')
            ->where('id', $role)
            ->first();

        abort_unless($roleRecord, 404);

        $permissions = $this->permissions();

        $assignedPermissions = DB::table('role_permissions')
            ->where('role_id', $role)
            ->pluck('permission_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return view('admin.roles.edit', compact(
            'roleRecord',
            'permissions',
            'assignedPermissions'
        ));
    }

    public function update(
        UpdateRoleRequest $request,
        int $role
    ): RedirectResponse {
        try {
            $this->roleService->update(
                $role,
                $request->validated()
            );

            return redirect()
                ->route('admin.roles.show', $role)
                ->with('success', 'Role updated successfully.');
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update the role.'
                );
        }
    }

    public function toggleStatus(int $role): RedirectResponse
    {
        try {
            $active = $this->roleService->toggleStatus($role);

            return back()->with(
                'success',
                $active
                    ? 'Role activated successfully.'
                    : 'Role deactivated successfully.'
            );
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }
    }

    public function destroy(int $role): RedirectResponse
    {
        try {
            $this->roleService->delete($role);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }
    }

    private function permissions()
    {
        return DB::table('permissions')
            ->orderBy('module')
            ->orderBy('permission_key')
            ->get();
    }
}
