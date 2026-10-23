<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'uuid',
        'booking_number',
        'client_id',
        'status',
        'quotation_required',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'balance_amount',
        'client_notes',
        'internal_notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'quotation_required' => 'boolean',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            ClientProfile::class,
            'client_id'
        );
    }

    public function events(): HasMany
    {
        return $this->hasMany(
            BookingEvent::class,
            'booking_id'
        );
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(
            Quotation::class,
            'booking_id'
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(
            Invoice::class,
            'booking_id'
        );
    }
}
