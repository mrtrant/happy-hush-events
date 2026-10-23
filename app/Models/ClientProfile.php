<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientProfile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'client_profiles';

    protected $fillable = [
        'uuid',
        'user_id',
        'client_number',
        'client_type',
        'organization_name',
        'kra_pin',
        'website',
        'preferred_contact_method',
        'notes',
    ];

    protected $casts = [
        'client_type' => 'string',
        'preferred_contact_method' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(
            ClientContact::class,
            'client_id'
        );
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(
            ClientAddress::class,
            'client_id'
        );
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(
            Booking::class,
            'client_id'
        );
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(
            Quotation::class,
            'client_id'
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(
            Invoice::class,
            'client_id'
        );
    }
}
