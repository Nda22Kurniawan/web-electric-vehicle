<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
        'max_appointments',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_closed' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    /**
     * Get the day name in Indonesian.
     *
     * @return string
     */
    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return $days[$this->day_of_week] ?? 'Tidak diketahui';
    }
}