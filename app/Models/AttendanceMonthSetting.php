<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceMonthSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance_month_settings';

    protected $fillable = [
        'month_name',
        'order'
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the attendance records for this month.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(StudentAcademicAttendance::class, 'attendance_month_id');
    }



    /**
     * Scope a query to only include active months.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to order by the order field.
     */
    public function scopeOrdered($query, $direction = 'asc')
    {
        return $query->orderBy('order', $direction)
                    ->orderBy('month_name', $direction);
    }

    /**
     * Get the next available order value.
     */
    public static function getNextOrder()
    {
        return self::max('order') + 1;
    }
}