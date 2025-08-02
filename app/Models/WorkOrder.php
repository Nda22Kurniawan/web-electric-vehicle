<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WorkOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'customer_id',
        'vehicle_id',
        'mechanic_id',
        'work_order_number',
        'start_time',
        'end_time',
        'status',
        'diagnosis',
        'total_amount',
        'payment_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the work order.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the appointment that belongs to the work order.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the vehicle that belongs to the work order.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the mechanic that is assigned to the work order.
     */
    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    /**
     * Get the services for the work order.
     */
    public function services()
    {
        return $this->hasMany(WorkOrderService::class);
    }

    /**
     * Get the parts for the work order.
     */
    public function parts()
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    /**
     * Get the payments for the work order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the customer feedback for the work order.
     */
    public function feedback()
    {
        return $this->hasOne(CustomerFeedback::class);
    }

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate work order number before creating a new work order
        static::creating(function ($workOrder) {
            $workOrder->work_order_number = static::generateWorkOrderNumber();
        });
    }
    
    /**
     * Generate a unique work order number.
     *
     * @return string
     */
    protected static function generateWorkOrderNumber()
    {
        $prefix = 'WO';
        $random = strtoupper(Str::random(3));
        $dateCode = date('ymd');
        $workOrderNumber = $prefix . $dateCode . $random;
        
        // Ensure work order number is unique
        while (static::where('work_order_number', $workOrderNumber)->exists()) {
            $random = strtoupper(Str::random(3));
            $workOrderNumber = $prefix . $dateCode . $random;
        }
        
        return $workOrderNumber;
    }
    
    /**
     * Calculate the total amount of the work order.
     *
     * @return float
     */
    public function calculateTotal()
    {
        $servicesTotal = $this->services()->sum(DB::raw('price * quantity'));
        $partsTotal = $this->parts()->sum(DB::raw('price * quantity'));
        
        return $servicesTotal + $partsTotal;
    }
    
    /**
     * Update the total amount of the work order.
     */
    public function updateTotal()
    {
        $this->total_amount = $this->calculateTotal();
        $this->save();
    }
    
    /**
     * Get the total paid amount.
     *
     * @return float
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }
    
    /**
     * Get the remaining balance.
     *
     * @return float
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->total_paid;
    }
    
    /**
     * Update payment status based on payments.
     */
    public function updatePaymentStatus()
    {
        $totalPaid = $this->total_paid;
        
        if ($totalPaid <= 0) {
            $status = 'unpaid';
        } elseif ($totalPaid < $this->total_amount) {
            $status = 'partial';
        } else {
            $status = 'paid';
        }
        
        $this->payment_status = $status;
        $this->save();
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