<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [

        'uuid',

        'name',

        'contact_person',

        'phone',

        'address',

        'city',

        'county',

        'latitude',

        'longitude',

        'capacity',

        'notes',

        'is_active',

    ];

    protected $casts = [

        'is_active' => 'boolean',

    ];

    public function bookingEvents(): HasMany
    {
        return $this->hasMany(
            BookingEvent::class
        );
    }
}
