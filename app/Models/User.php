<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [

        'uuid',

        'user_code',

        'first_name',

        'middle_name',

        'last_name',

        'email',

        'phone',

        'password',

        'profile_photo',

        'email_verified_at',

        'last_login_at',

        'failed_login_attempts',

        'account_locked_until',

        'is_active',

        'remember_token',

        'created_by',

        'updated_by',

    ];

    protected $hidden = [

        'password',

        'remember_token',

    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'last_login_at' => 'datetime',

            'account_locked_until' => 'datetime',

            'password' => 'hashed',

            'is_active' => 'boolean',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(

            Role::class,

            'user_roles',

            'user_id',

            'role_id'

        )->withPivot([

            'assigned_by',

            'assigned_at',

        ]);
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function fullName(): string
    {
        return trim(

            $this->first_name . ' ' .

            ($this->middle_name ?? '') . ' ' .

            $this->last_name

        );
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()

            ->where('name', $role)

            ->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()

            ->whereIn('name', $roles)

            ->exists();
    }

    public function isAdministrator(): bool
    {
        return $this->hasRole('Administrator');
    }

    public function isManager(): bool
    {
        return $this->hasRole('Manager');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('Staff');
    }

    public function isClient(): bool
    {
        return $this->hasRole('Client');
    }
}
