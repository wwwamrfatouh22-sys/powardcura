<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id',
        'payment_method',
        'transaction_id',
        'reference_number',
        'amount',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['confirmed', 'paid'], true);
    }
}
