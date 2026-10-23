<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientContact extends Model
{
    use HasFactory;

    protected $table = 'client_contacts';

    protected $fillable = [
        'uuid',
        'client_id',
        'first_name',
        'last_name',
        'job_title',
        'email',
        'phone',
        'alternative_phone',
        'is_primary',
        'receives_notifications',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'receives_notifications' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            ClientProfile::class,
            'client_id'
        );
    }
}
