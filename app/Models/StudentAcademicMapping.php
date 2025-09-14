<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademicMapping extends Model
{
    use HasFactory;

    protected $table = 'student_academic_mappings';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade_id',
        'stream_id',
        'shift_id',
        'section_id',
        'is_active_year',
    ];

    protected $casts = [
        'is_active_year' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the mapping.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year for the mapping.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYearSetting::class, 'academic_year_id');
    }

    /**
     * Get the grade for the mapping.
     */
    public function grade()
    {
        return $this->belongsTo(GradeSetting::class, 'grade_id');
    }

    /**
     * Get the stream for the mapping.
     */
    public function stream()
    {
        return $this->belongsTo(StreamSetting::class, 'stream_id');
    }

    /**
     * Get the shift for the mapping.
     */
    public function shift()
    {
        return $this->belongsTo(ShiftSetting::class, 'shift_id');
    }

    /**
     * Get the section for the mapping.
     */
    public function section()
    {
        return $this->belongsTo(SectionSetting::class, 'section_id');
    }

    /**
     * Scope a query to only include active year mappings.
     */
    public function scopeActiveYear($query)
    {
        return $query->where('is_active_year', true);
    }

    /**
     * Scope a query to include mappings for a specific academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }


        public function month()
    {
        return $this->belongsTo(AttendanceMonthSetting::class, 'attendance_month_id');
    }


}