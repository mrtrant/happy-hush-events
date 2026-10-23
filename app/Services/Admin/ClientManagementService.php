<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClientManagementService
{
    private const CLIENT_TYPES = [
        'Individual',
        'Company',
        'Organization',
        'School',
        'Church',
        'Government',
    ];

    private const CONTACT_METHODS = [
        'Email',
        'Phone',
        'SMS',
        'WhatsApp',
    ];

    /**
     * Update a client and the associated user account.
     */
    public function update(int $clientId, array $data): void
    {
        $client = DB::table('client_profiles')
            ->where('id', $clientId)
            ->whereNull('deleted_at')
            ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'client' => 'Client could not be found.',
            ]);
        }

        $user = DB::table('users')
            ->where('id', $client->user_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'client' => 'The user account associated with this client could not be found.',
            ]);
        }

        $validator = Validator::make($data, [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'client_type' => [
                'required',
                Rule::in(self::CLIENT_TYPES),
            ],
            'organization_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'kra_pin' => [
                'nullable',
                'string',
                'max:30',
            ],
            'website' => [
                'nullable',
                'url',
                'max:255',
            ],
            'preferred_contact_method' => [
                'nullable',
                Rule::in(self::CONTACT_METHODS),
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated = $validator->validate();

        DB::transaction(function () use ($client, $user, $validated): void {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            DB::table('client_profiles')
                ->where('id', $client->id)
                ->update([
                    'client_type' => $validated['client_type'],
                    'organization_name' => $validated['organization_name'] ?? null,
                    'kra_pin' => $validated['kra_pin'] ?? null,
                    'website' => $validated['website'] ?? null,
                    'preferred_contact_method' => $validated['preferred_contact_method'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * Activate or deactivate the client's user account.
     */
    public function toggleStatus(int $clientId): bool
    {
        $client = DB::table('client_profiles')
            ->where('id', $clientId)
            ->whereNull('deleted_at')
            ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'client' => 'Client could not be found.',
            ]);
        }

        $user = DB::table('users')
            ->where('id', $client->user_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'client' => 'The user account associated with this client could not be found.',
            ]);
        }

        $newStatus = !(bool) $user->is_active;

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'is_active' => $newStatus,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

        return $newStatus;
    }

    public function clientTypes(): array
    {
        return self::CLIENT_TYPES;
    }

    public function contactMethods(): array
    {
        return self::CONTACT_METHODS;
    }
}
