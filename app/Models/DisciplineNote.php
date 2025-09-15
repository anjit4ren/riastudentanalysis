<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DisciplineNote extends Model
{
    use HasFactory;

    protected $table = 'discipline_notes';

    protected $fillable = [
        'student_id',
        'academic_map_id',
        'note',
        'interactor',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        
    ];

    /**
     * Get the student that owns the discipline note.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic mapping for the discipline note.
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
     * Scope a query to filter by interactor.
     */
    public function scopeByInteractor($query, $interactor)
    {
        return $query->where('interactor', 'like', '%' . $interactor . '%');
    }
}