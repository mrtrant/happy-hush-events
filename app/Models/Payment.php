<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'receipt_number',
        'invoice_id',
        'payment_method_id',
        'amount',
        'transaction_reference',
        'payment_date',
        'notes',
        'received_by',
        'created_at',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'payment_method_id' => 'integer',
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'received_by' => 'integer',
        'created_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(
            PaymentMethod::class,
            'payment_method_id'
        );
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'received_by'
        );
    }
}
