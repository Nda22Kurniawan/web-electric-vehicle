<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the work order that owns the payment.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Available payment methods.
     *
     * @return array
     */
    public static function paymentMethods()
    {
        return [
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'credit_card' => 'Kartu Kredit',
            'other' => 'Lainnya'
        ];
    }

    /**
     * Get the payment method name.
     *
     * @return string
     */
    public function getPaymentMethodNameAttribute()
    {
        return self::paymentMethods()[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Update work order payment status when a payment is added, updated, or deleted
        static::saved(function ($payment) {
            $payment->workOrder->updatePaymentStatus();
        });
        
        static::deleted(function ($payment) {
            $payment->workOrder->updatePaymentStatus();
        });
    }
}