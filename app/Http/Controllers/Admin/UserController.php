<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use RuntimeException;

class UserController extends Controller
{
    public function __construct(
        private readonly UserManagementService $userService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');
        $role = $request->input('role');

        $users = DB::table('users')
            ->select('users.*')
            ->selectSub(
                DB::table('user_roles')
                    ->join(
                        'roles',
                        'roles.id',
                        '=',
                        'user_roles.role_id'
                    )
                    ->whereColumn(
                        'user_roles.user_id',
                        'users.id'
                    )
                    ->selectRaw(
                        "GROUP_CONCAT(
                            roles.name
                            ORDER BY roles.name
                            SEPARATOR ', '
                        )"
                    ),
                'role_names'
            )
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where(
                                'users.first_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.middle_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.last_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.phone',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.user_code',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $status === 'active',
                fn ($query) => $query->where('users.is_active', true)
            )
            ->when(
                $status === 'inactive',
                fn ($query) => $query->where(
                    'users.is_active',
                    false
                )
            )
            ->when(
                $role,
                function ($query) use ($role): void {
                    $query->whereExists(function ($subQuery) use ($role) {
                        $subQuery
                            ->select(DB::raw(1))
                            ->from('user_roles')
                            ->whereColumn(
                                'user_roles.user_id',
                                'users.id'
                            )
                            ->where(
                                'user_roles.role_id',
                                $role
                            );
                    });
                }
            )
            ->whereNull('users.deleted_at')
            ->orderByDesc('users.id')
            ->paginate(15)
            ->withQueryString();

        $roles = DB::table('roles')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact(
            'users',
            'roles',
            'search',
            'status',
            'role'
        ));
    }

    public function create(): View
    {
        $roles = DB::table('roles')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $this->userService->create(
                $request->validated(),
                $request->file('profile_photo')
            );

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create the user. Please try again.'
                );
        }
    }

    public function show(int $user): View
    {
        $userRecord = DB::table('users')
            ->where('users.id', $user)
            ->whereNull('users.deleted_at')
            ->first();

        abort_unless($userRecord, 404);

        $roles = DB::table('roles')
            ->join(
                'user_roles',
                'roles.id',
                '=',
                'user_roles.role_id'
            )
            ->where('user_roles.user_id', $user)
            ->orderBy('roles.name')
            ->select('roles.*')
            ->get();

        return view('admin.users.show', [
            'user' => $userRecord,
            'roles' => $roles,
        ]);
    }

    public function edit(int $user): View
    {
        $userRecord = DB::table('users')
            ->where('id', $user)
            ->whereNull('deleted_at')
            ->first();

        abort_unless($userRecord, 404);

        $roles = DB::table('roles')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assignedRoles = DB::table('user_roles')
            ->where('user_id', $user)
            ->pluck('role_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return view('admin.users.edit', [
            'user' => $userRecord,
            'roles' => $roles,
            'assignedRoles' => $assignedRoles,
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        int $user
    ): RedirectResponse {
        try {
            $this->userService->update(
                $user,
                $request->validated(),
                $request->file('profile_photo')
            );

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');
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
                    'Unable to update the user. Please try again.'
                );
        }
    }

    public function toggleStatus(int $user): RedirectResponse
    {
        try {
            $active = $this->userService->toggleStatus($user);

            return back()->with(
                'success',
                $active
                    ? 'User activated successfully.'
                    : 'User deactivated successfully.'
            );
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'Unable to change the user status.'
            );
        }
    }

    public function destroy(int $user): RedirectResponse
    {
        try {
            $this->userService->delete($user);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'Unable to delete the user.'
            );
        }
    }

    public function sendPasswordReset(int $user): RedirectResponse
    {
        $userRecord = DB::table('users')
            ->where('id', $user)
            ->whereNull('deleted_at')
            ->first();

        abort_unless($userRecord, 404);

        $status = Password::sendResetLink([
            'email' => $userRecord->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'Password reset link has been sent to ' . $userRecord->email
            );
        }

        return back()->with('error', __($status));
    }
}
