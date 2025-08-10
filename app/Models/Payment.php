<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'amount',
        'payment_method',
        'transaction_reference'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getPaymentMethodNameAttribute()
    {
        $methods = [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other'
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }
}