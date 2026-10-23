<?php

namespace App\Services\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class UserManagementService
{
    public function create(array $data, ?UploadedFile $photo = null): int
    {
        $uploadedPhoto = null;

        try {
            if ($photo) {
                $uploadedPhoto = $photo->store('profiles', 'public');
            }

            $userId = DB::transaction(function () use ($data, $uploadedPhoto): int {
                $userId = DB::table('users')->insertGetId([
                    'uuid' => (string) Str::uuid(),
                    'user_code' => $this->generateUserCode(),
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => Hash::make($data['password']),
                    'profile_photo' => $uploadedPhoto,
                    'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->syncRoles($userId, $data['roles']);

                return $userId;
            });

            return $userId;
        } catch (\Throwable $exception) {
            if ($uploadedPhoto) {
                Storage::disk('public')->delete($uploadedPhoto);
            }

            throw $exception;
        }
    }

    public function update(
        int $userId,
        array $data,
        ?UploadedFile $photo = null
    ): void {
        $oldPhoto = DB::table('users')
            ->where('id', $userId)
            ->value('profile_photo');

        $newPhoto = null;

        try {
            if ($photo) {
                $newPhoto = $photo->store('profiles', 'public');
            }

            DB::transaction(function () use (
                $userId,
                $data,
                $newPhoto
            ): void {
                $update = [
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'is_active' => isset($data['is_active'])
                        ? (bool) $data['is_active']
                        : false,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ];

                if (!empty($data['password'])) {
                    $update['password'] = Hash::make($data['password']);
                }

                if ($newPhoto) {
                    $update['profile_photo'] = $newPhoto;
                }

                DB::table('users')
                    ->where('id', $userId)
                    ->update($update);

                $this->validateAdminRoleChange(
                    $userId,
                    $data['roles']
                );

                $this->syncRoles($userId, $data['roles']);
            });

            if ($newPhoto && $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
        } catch (\Throwable $exception) {
            if ($newPhoto) {
                Storage::disk('public')->delete($newPhoto);
            }

            throw $exception;
        }
    }

    public function toggleStatus(int $userId): bool
    {
        if ($userId === (int) auth()->id()) {
            throw new RuntimeException(
                'You cannot deactivate your own account.'
            );
        }

        return DB::transaction(function () use ($userId): bool {
            $user = DB::table('users')
                ->where('id', $userId)
                ->whereNull('deleted_at')
                ->first();

            if (!$user) {
                throw new RuntimeException('User not found.');
            }

            $newStatus = !(bool) $user->is_active;

            if (!$newStatus && $this->isLastActiveAdmin($userId)) {
                throw new RuntimeException(
                    'The last active Administrator cannot be deactivated.'
                );
            }

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'is_active' => $newStatus,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            return $newStatus;
        });
    }

    public function delete(int $userId): void
    {
        if ($userId === (int) auth()->id()) {
            throw new RuntimeException(
                'You cannot delete your own account.'
            );
        }

        DB::transaction(function () use ($userId): void {
            $user = DB::table('users')
                ->where('id', $userId)
                ->whereNull('deleted_at')
                ->first();

            if (!$user) {
                throw new RuntimeException('User not found.');
            }

            if ($this->isLastActiveAdmin($userId)) {
                throw new RuntimeException(
                    'The last active Administrator cannot be deleted.'
                );
            }

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'is_active' => false,
                    'deleted_at' => now(),
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);
        });
    }

    private function syncRoles(int $userId, array $roleIds): void
    {
        DB::table('user_roles')
            ->where('user_id', $userId)
            ->delete();

        $rows = [];

        foreach (array_unique($roleIds) as $roleId) {
            $rows[] = [
                'user_id' => $userId,
                'role_id' => (int) $roleId,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ];
        }

        if ($rows) {
            DB::table('user_roles')->insert($rows);
        }
    }

    private function validateAdminRoleChange(
        int $userId,
        array $roleIds
    ): void {
        $adminRoleId = DB::table('roles')
            ->where('name', 'Admin')
            ->value('id');

        if (!$adminRoleId) {
            return;
        }

        $currentlyAdmin = DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $adminRoleId)
            ->exists();

        $willRemainAdmin = in_array(
            (int) $adminRoleId,
            array_map('intval', $roleIds),
            true
        );

        if (
            $currentlyAdmin
            && !$willRemainAdmin
            && $this->isLastActiveAdmin($userId)
        ) {
            throw new RuntimeException(
                'The last active Administrator role cannot be removed.'
            );
        }
    }

    private function isLastActiveAdmin(int $excludeUserId = 0): bool
    {
        $adminRoleId = DB::table('roles')
            ->where('name', 'Admin')
            ->value('id');

        if (!$adminRoleId) {
            return false;
        }

        return DB::table('users')
            ->join(
                'user_roles',
                'user_roles.user_id',
                '=',
                'users.id'
            )
            ->where('user_roles.role_id', $adminRoleId)
            ->where('users.is_active', true)
            ->whereNull('users.deleted_at')
            ->when(
                $excludeUserId > 0,
                fn ($query) => $query->where(
                    'users.id',
                    '!=',
                    $excludeUserId
                )
            )
            ->count() === 0;
    }

    private function generateUserCode(): string
    {
        do {
            $code = 'USR-' . strtoupper(Str::random(10));
        } while (
            DB::table('users')
                ->where('user_code', $code)
                ->exists()
        );

        return $code;
    }
}
