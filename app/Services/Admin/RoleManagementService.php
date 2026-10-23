<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RoleManagementService
{
    public function create(array $data): int
    {
        return DB::transaction(function () use ($data): int {
            return DB::table('roles')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'name' => trim($data['name']),
                'description' => $data['description'] ?? null,
                'is_active' => isset($data['is_active'])
                    ? (bool) $data['is_active']
                    : true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function update(int $roleId, array $data): void
    {
        DB::transaction(function () use ($roleId, $data): void {
            $role = DB::table('roles')
                ->where('id', $roleId)
                ->first();

            if (!$role) {
                throw new RuntimeException('Role not found.');
            }

            DB::table('roles')
                ->where('id', $roleId)
                ->update([
                    'name' => trim($data['name']),
                    'description' => $data['description'] ?? null,
                    'is_active' => isset($data['is_active'])
                        ? (bool) $data['is_active']
                        : false,
                    'updated_at' => now(),
                ]);

            $this->syncPermissions(
                $roleId,
                $data['permissions'] ?? []
            );
        });
    }

    public function syncPermissions(
        int $roleId,
        array $permissionIds
    ): void {
        DB::table('role_permissions')
            ->where('role_id', $roleId)
            ->delete();

        $permissionIds = array_unique(
            array_map('intval', $permissionIds)
        );

        if (!$permissionIds) {
            return;
        }

        $rows = [];

        foreach ($permissionIds as $permissionId) {
            $rows[] = [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ];
        }

        DB::table('role_permissions')->insert($rows);
    }

    public function toggleStatus(int $roleId): bool
    {
        return DB::transaction(function () use ($roleId): bool {
            $role = DB::table('roles')
                ->where('id', $roleId)
                ->first();

            if (!$role) {
                throw new RuntimeException('Role not found.');
            }

            if (
                $role->name === 'Admin'
                && (bool) $role->is_active
            ) {
                $activeAdmins = DB::table('users')
                    ->join(
                        'user_roles',
                        'user_roles.user_id',
                        '=',
                        'users.id'
                    )
                    ->where('user_roles.role_id', $roleId)
                    ->where('users.is_active', true)
                    ->whereNull('users.deleted_at')
                    ->count();

                if ($activeAdmins > 0) {
                    throw new RuntimeException(
                        'The Administrator role cannot be deactivated while active users are assigned to it.'
                    );
                }
            }

            $newStatus = !(bool) $role->is_active;

            DB::table('roles')
                ->where('id', $roleId)
                ->update([
                    'is_active' => $newStatus,
                    'updated_at' => now(),
                ]);

            return $newStatus;
        });
    }

    public function delete(int $roleId): void
    {
        DB::transaction(function () use ($roleId): void {
            $role = DB::table('roles')
                ->where('id', $roleId)
                ->first();

            if (!$role) {
                throw new RuntimeException('Role not found.');
            }

            $assignedUsers = DB::table('user_roles')
                ->where('role_id', $roleId)
                ->count();

            if ($assignedUsers > 0) {
                throw new RuntimeException(
                    'This role cannot be deleted because users are assigned to it. Reassign those users first.'
                );
            }

            DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->delete();

            DB::table('roles')
                ->where('id', $roleId)
                ->delete();
        });
    }
}
