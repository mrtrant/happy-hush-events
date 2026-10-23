<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingEventService extends Model
{
    protected $table = 'booking_event_services';

    public $timestamps = false;

    protected $fillable = [
        'booking_event_id',
        'service_id',
        'source',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'booking_event_id' => 'integer',
        'service_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Event this service belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(
            BookingEvent::class,
            'booking_event_id'
        );
    }

    /**
     * Service assigned to the event.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(
            Service::class,
            'service_id'
        );
    }
}
