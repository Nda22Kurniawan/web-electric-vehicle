<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'customer_name', // nullable untuk backward compatibility
        'customer_phone', // nullable untuk backward compatibility
        'type',
        'brand',
        'model',
        'year',
        'license_plate',
        'color',
        'notes',
    ];

    /**
     * Get the customer that owns the vehicle.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the appointments for the vehicle.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the work orders for the vehicle.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get customer name (from relationship or fallback to stored value)
     */
    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->name : $this->attributes['customer_name'];
    }

    /**
     * Get customer phone (from relationship or fallback to stored value)
     */
    public function getCustomerPhoneAttribute()
    {
        return $this->customer ? $this->customer->phone : $this->attributes['customer_phone'];
    }
}