<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'eid',
        'name',
        'roll_no',
        'address',
        'previous_school',
        'see_gpa',
        'parents_name',
        'parents_contact',
        'photo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the academic mappings for the student.
     */
    public function academicMappings()
    {
        return $this->hasMany(StudentAcademicMapping::class);
    }

    /**
     * Get the current active academic mapping.
     */
    public function currentAcademicMapping()
    {
        return $this->hasOne(StudentAcademicMapping::class)
            ->where('is_active_year', true)
            ->with(['academicYear', 'grade', 'stream', 'shift', 'section']);
    }

  
  

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include inactive students.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    
}
