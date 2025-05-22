<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'part_id',
        'work_order_id',
        'quantity',
        'transaction_type',
        'notes',
    ];

    /**
     * Get the part that belongs to the transaction.
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Get the work order that belongs to the transaction.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get transaction type label.
     *
     * @return string
     */
    public function getTransactionTypeNameAttribute()
    {
        $types = [
            'purchase' => 'Pembelian',
            'sales' => 'Penjualan',
            'adjustment' => 'Penyesuaian',
            'return' => 'Pengembalian'
        ];
        
        return $types[$this->transaction_type] ?? $this->transaction_type;
    }

    /**
     * Get the transaction direction for display purposes.
     *
     * @return string
     */
    public function getDirectionAttribute()
    {
        return $this->quantity > 0 ? 'in' : 'out';
    }

    /**
     * Get the absolute quantity value for display purposes.
     *
     * @return int
     */
    public function getAbsoluteQuantityAttribute()
    {
        return abs($this->quantity);
    }
    
    /**
     * Scope a query to only include incoming transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncoming($query)
    {
        return $query->where('quantity', '>', 0);
    }
    
    /**
     * Scope a query to only include outgoing transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutgoing($query)
    {
        return $query->where('quantity', '<', 0);
    }
    
    /**
     * Scope a query to only include transactions of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }
}