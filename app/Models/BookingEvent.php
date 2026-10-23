<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingEvent extends Model
{
    use HasFactory;

    protected $table = 'booking_events';

    protected $fillable = [
        'uuid',
        'booking_id',
        'event_type_id',
        'venue_id',
        'package_id',
        'event_name',
        'event_date',
        'start_time',
        'end_time',
        'guest_count',
        'status',
        'special_requirements',
    ];

    protected $casts = [
        'event_date' => 'date',
        'guest_count' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(
            Booking::class,
            'booking_id'
        );
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(
            EventType::class,
            'event_type_id'
        );
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(
            Venue::class,
            'venue_id'
        );
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(
            Package::class,
            'package_id'
        );
    }

    public function services(): HasMany
    {
        return $this->hasMany(
            BookingEventService::class,
            'booking_event_id'
        );
    }

    public function staff(): HasMany
    {
        return $this->hasMany(
            BookingEventStaff::class,
            'booking_event_id'
        );
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(
            BookingEventInventory::class,
            'booking_event_id'
        );
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(
            BookingEventTimeline::class,
            'booking_event_id'
        );
    }
}
