<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingEventTimeline extends Model
{
    use HasFactory;

    protected $table = 'booking_event_timelines';

    public $timestamps = false;

    protected $fillable = [
        'booking_event_id',
        'title',
        'description',
        'completed',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(
            BookingEvent::class,
            'booking_event_id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
