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
        'customer_name',
        'customer_phone',
        'type',
        'brand',
        'model',
        'year',
        'license_plate',
        'color',
        'notes',
    ];

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
}