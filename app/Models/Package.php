<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'packages';

    protected $fillable = [
        'uuid',
        'package_code',
        'event_type_id',
        'name',
        'slug',
        'description',
        'package_price',
        'discount_percentage',
        'duration_minutes',
        'image',
        'is_active',
    ];

    protected $casts = [
        'package_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'package_services',
            'package_id',
            'service_id'
        )->withPivot([
            'quantity',
            'custom_price',
            'notes',
        ]);
    }
}
