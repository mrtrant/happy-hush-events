<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'login_time',
        'logout_time',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'operating_system',
        'login_status',
    ];

    protected function casts(): array
    {
        return [
            'login_time' => 'datetime',
            'logout_time' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}