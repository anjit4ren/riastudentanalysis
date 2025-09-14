<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_settings';

    protected $fillable = [
        'title',
        'academic_year_id',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the academic year that owns the exam setting.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYearSetting::class, 'academic_year_id');
    }

    /**
     * Scope a query to only include active exam settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to include exam settings for a specific academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to include exam settings for current active academic year.
     */
    public function scopeForCurrentAcademicYear($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->where('running', true);
        });
    }

    /**
     * Scope a query to order by academic year and title.
     */
    public function scopeOrdered($query, $direction = 'asc')
    {
        return $query->orderBy('academic_year_id', $direction)
                    ->orderBy('title', $direction);
    }

    /**
     * Get the full exam name with academic year context.
     */
    public function getFullExamNameAttribute()
    {
        if ($this->academicYear) {
            return "{$this->title} - {$this->academicYear->name}";
        }
        
        return $this->title;
    }

    /**
     * Check if the exam setting is for the current academic year.
     */
    public function getIsForCurrentAcademicYearAttribute()
    {
        return $this->academicYear && $this->academicYear->running;
    }

    /**
     * Activate the exam setting.
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the exam setting.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }
}