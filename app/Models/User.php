<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is mechanic
     */
    public function isMechanic()
    {
        return $this->role === 'mechanic';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Get the vehicles owned by the customer.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'customer_id');
    }

    /**
     * Get the appointments made by the customer.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    /**
     * Get the work orders for the customer.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'customer_id');
    }

    /**
     * Get the work orders assigned to the mechanic.
     */
    public function mechanicWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'mechanic_id');
    }

    /**
     * Get the feedback given by the customer.
     */
    public function feedback()
    {
        return $this->hasMany(CustomerFeedback::class, 'customer_id');
    }
}
