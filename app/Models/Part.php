<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'part_number',
        'price',
        'cost',
        'stock',
        'min_stock',
        'vehicle_type',
    ];

    /**
     * Get the work order parts for this part.
     */
    public function workOrderParts()
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    /**
     * Get the inventory transactions for this part.
     */
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
    
    /**
     * Determine if the part is low on stock.
     *
     * @return bool
     */
    public function isLowStock()
    {
        return $this->stock <= $this->min_stock;
    }
}