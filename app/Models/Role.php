<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [

        'uuid',

        'name',

        'description',

        'is_active',

    ];

    protected function casts(): array
    {
        return [

            'is_active' => 'boolean',

        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(

            User::class,

            'user_roles',

            'role_id',

            'user_id'

        )->withPivot([

            'assigned_by',

            'assigned_at',

        ]);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(

            Permission::class,

            'role_permissions',

            'role_id',

            'permission_id'

        );
    }
}
