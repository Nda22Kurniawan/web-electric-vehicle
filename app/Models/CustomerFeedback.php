<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'customer_id',
        'customer_name', // nullable untuk backward compatibility
        'rating',
        'comment',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'is_public' => 'boolean',
    ];

    /**
     * Get the customer that gave the feedback.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the work order associated with the feedback.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Scope a query to only include public feedback.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get the star rating as HTML.
     *
     * @return string
     */
    public function getStarRatingAttribute()
    {
        $html = '';
        
        // Full stars
        for ($i = 1; $i <= $this->rating; $i++) {
            $html .= '<i class="fas fa-star text-warning"></i>';
        }
        
        // Empty stars
        for ($i = $this->rating + 1; $i <= 5; $i++) {
            $html .= '<i class="far fa-star text-warning"></i>';
        }
        
        return $html;
    }
    
    /**
     * Get rating text.
     *
     * @return string
     */
    public function getRatingTextAttribute()
    {
        $ratings = [
            1 => 'Sangat Buruk',
            2 => 'Buruk',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik'
        ];
        
        return $ratings[$this->rating] ?? 'Tidak Ada Rating';
    }

    /**
     * Get customer name (from relationship or fallback to stored value)
     */
    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->name : $this->attributes['customer_name'];
    }
}