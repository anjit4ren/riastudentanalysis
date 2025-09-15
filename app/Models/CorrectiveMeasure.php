<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorrectiveMeasure extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'corrective_measures';

    protected $fillable = [
        'student_id',
        'academic_map_id',
        'measure',
        'reason',
        'implemented_at',
        'resolved_at'
    ];

    protected $casts = [
        'implemented_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the student that owns the corrective measure.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic mapping for the corrective measure.
     */
    public function academicMapping()
    {
        return $this->belongsTo(StudentAcademicMapping::class, 'academic_map_id');
    }

    /**
     * Scope a query to filter by academic mapping.
     */
    public function scopeForAcademicMapping($query, $academicMapId)
    {
        return $query->where('academic_map_id', $academicMapId);
    }

    /**
     * Scope a query to filter by student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include active (not resolved) measures.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope a query to only include resolved measures.
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Check if the measure is resolved.
     */
    public function isResolved()
    {
        return !is_null($this->resolved_at);
    }
}