<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademicAttendance extends Model
{
    use HasFactory;

    protected $table = 'student_academic_attendance_mappings';

    protected $fillable = [
        'student_id',
        'academic_map_id',
        'attendance_month_id',
        'present_days',
        'late_days',
        'absent_days',
        'school_days'
    ];

    protected $casts = [
        'present_days' => 'integer',
        'late_days' => 'integer',
        'absent_days' => 'integer',
        'school_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Calculate attendance percentage.
     */
    public function getAttendancePercentageAttribute()
    {
        if ($this->school_days > 0) {
            return round(($this->present_days / $this->school_days) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the total attended days (present + late).
     */
    public function getTotalAttendedDaysAttribute()
    {
        return $this->present_days + $this->late_days;
    }

    /**
     * Get the student that owns the attendance record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic mapping that owns the attendance record.
     */
    public function academicMapping()
    {
        return $this->belongsTo(StudentAcademicMapping::class, 'academic_map_id');
    }

    /**
     * Get the attendance month that owns the attendance record.
     */
    public function attendanceMonth()
    {
        return $this->belongsTo(AttendanceMonthSetting::class, 'attendance_month_id');
    }

    /**
     * Scope a query to include attendance for a specific academic mapping.
     */
    public function scopeForAcademicMapping($query, $academicMapId)
    {
        return $query->where('academic_map_id', $academicMapId);
    }

    /**
     * Scope a query to include attendance for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to include attendance for a specific month.
     */
    public function scopeForMonth($query, $monthId)
    {
        return $query->where('attendance_month_id', $monthId);
    }

    /**
     * Scope a query to include attendance for a specific academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->whereHas('academicMapping', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    /**
     * Scope a query to include attendance with minimum percentage.
     */
    public function scopeWithMinPercentage($query, $percentage)
    {
        return $query->whereRaw('(present_days / school_days) * 100 >= ?', [$percentage]);
    }

    /**
     * Validate attendance data consistency.
     */
    public function validateAttendanceData()
    {
        $totalDays = $this->present_days + $this->late_days + $this->absent_days;
        
        if ($totalDays > $this->school_days) {
            return false;
        }

        return true;
    }
}