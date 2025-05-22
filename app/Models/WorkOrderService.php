<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'service_id',
        'quantity',
        'price',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the work order that owns this service.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the service associated with this work order service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the subtotal for this work order service.
     *
     * @return float
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Update work order total amount when a service is added, updated, or deleted
        static::saved(function ($workOrderService) {
            $workOrderService->workOrder->updateTotal();
        });
        
        static::deleted(function ($workOrderService) {
            $workOrderService->workOrder->updateTotal();
        });
    }
}