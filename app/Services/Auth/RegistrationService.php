<?php

namespace App\Services\Auth;

use App\Models\ClientProfile;
use App\Models\Role;
use App\Models\User;
use App\Services\System\CodeGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistrationService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected CodeGeneratorService $codeGenerator
    ) {
    }

    /**
     * Register a new client.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([

                'uuid' => (string) Str::uuid(),

                'user_code' => $this->codeGenerator->generate(
                    'users',
                    'user_code',
                    'USR'
                ),

                'first_name' => $data['first_name'],

                'middle_name' => $data['middle_name'] ?? null,

                'last_name' => $data['last_name'],

                'email' => $data['email'],

                'phone' => $data['phone'],

                'password' => Hash::make($data['password']),

                'is_active' => true,

            ]);

            ClientProfile::create([

                'uuid' => (string) Str::uuid(),

                'user_id' => $user->id,

                'client_number' => $this->codeGenerator->generate(
                    'client_profiles',
                    'client_number',
                    'CLI'
                ),

                'client_type' => $data['client_type'],

                'organization_name' => $data['organization_name'] ?? null,

                'kra_pin' => $data['kra_pin'] ?? null,

                'website' => $data['website'] ?? null,

                'preferred_contact_method' => $data['preferred_contact_method'],

            ]);

            $clientRole = Role::where('name', 'Client')
                ->where('is_active', true)
                ->firstOrFail();

            $user->roles()->attach($clientRole->id, [

                'assigned_by' => null,

                'assigned_at' => now(),

            ]);

            Auth::login($user);

            return $user;
        });
    }
}
