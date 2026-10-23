<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'client_id',
        'name',
        'message',
        'rating',
        'image_path',
        'is_approved',
        'created_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(ClientProfile::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
