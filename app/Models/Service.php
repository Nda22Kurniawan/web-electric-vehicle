<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
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
        'price',
        'duration_estimate',
    ];

    /**
     * The categories that belong to the service.
     */
    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_service_category');
    }

    /**
     * Get the work order services for this service.
     */
    public function workOrderServices()
    {
        return $this->hasMany(WorkOrderService::class);
    }
}