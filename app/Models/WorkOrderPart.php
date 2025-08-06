<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderPart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'part_id',
        'quantity',
        'price',
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
     * Get the work order that owns the part.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the part associated with this work order part.
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Get the subtotal for this work order part.
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
        
        // Update work order total amount when a part is added, updated, or deleted
        static::saved(function ($workOrderPart) {
            $workOrderPart->workOrder->updateTotal();
        });
        
        static::deleted(function ($workOrderPart) {
            $workOrderPart->workOrder->updateTotal();
        });
        
        // Create inventory transaction when a part is added to work order
        static::created(function ($workOrderPart) {
            
            
            // Update part stock
            $part = $workOrderPart->part;
            $part->stock -= $workOrderPart->quantity;
            $part->save();
        });
        
        // Update inventory transaction when a part quantity is updated
        static::updated(function ($workOrderPart) {
            if ($workOrderPart->isDirty('quantity')) {
                $oldQuantity = $workOrderPart->getOriginal('quantity');
                $newQuantity = $workOrderPart->quantity;
                $difference = $oldQuantity - $newQuantity;
                
                // Create adjustment inventory transaction
                InventoryTransaction::create([
                    'part_id' => $workOrderPart->part_id,
                    'work_order_id' => $workOrderPart->work_order_id,
                    'quantity' => $difference, // Positive if decreasing quantity, negative if increasing
                    'transaction_type' => 'adjustment',
                    'notes' => 'Disesuaikan di Work Order #' . $workOrderPart->workOrder->work_order_number,
                ]);
                
                // Update part stock
                $part = $workOrderPart->part;
                $part->stock += $difference;
                $part->save();
            }
        });
        
        // Return parts to inventory when a work order part is deleted
        static::deleted(function ($workOrderPart) {
            // Create return inventory transaction
            InventoryTransaction::create([
                'part_id' => $workOrderPart->part_id,
                'work_order_id' => $workOrderPart->work_order_id,
                'quantity' => $workOrderPart->quantity, // Positive for incoming inventory
                'transaction_type' => 'return',
                'notes' => 'Dikembalikan dari Work Order #' . $workOrderPart->workOrder->work_order_number,
            ]);
            
            // Update part stock
            $part = $workOrderPart->part;
            $part->stock += $workOrderPart->quantity;
            $part->save();
        });
    }
}