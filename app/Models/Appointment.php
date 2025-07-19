<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
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
        'customer_email', // nullable untuk backward compatibility
        'vehicle_id',
        'appointment_date',
        'appointment_time',
        'service_description',
        'status',
        'notes',
        'tracking_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
    ];

    /**
     * Get the customer that owns the appointment.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the vehicle that owns the appointment.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the work order associated with the appointment.
     */
    public function workOrder()
    {
        return $this->hasOne(WorkOrder::class);
    }

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate tracking code before creating a new appointment
        static::creating(function ($appointment) {
            $appointment->tracking_code = static::generateTrackingCode();
        });
    }
    
    /**
     * Generate a unique tracking code.
     *
     * @return string
     */
    protected static function generateTrackingCode()
    {
        $prefix = 'APT';
        $random = strtoupper(Str::random(5));
        $dateCode = date('ymd');
        $trackingCode = $prefix . $dateCode . $random;
        
        // Ensure tracking code is unique
        while (static::where('tracking_code', $trackingCode)->exists()) {
            $random = strtoupper(Str::random(5));
            $trackingCode = $prefix . $dateCode . $random;
        }
        
        return $trackingCode;
    }
    
    /**
     * Scope a query to only include appointments with the given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include appointments for today.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', now()->toDateString());
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

    /**
     * Get customer email (from relationship or fallback to stored value)
     */
    public function getCustomerEmailAttribute()
    {
        return $this->customer ? $this->customer->email : $this->attributes['customer_email'];
    }
}