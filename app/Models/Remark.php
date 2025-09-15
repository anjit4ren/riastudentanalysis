<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remark extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'remarks';

    protected $fillable = [
        'student_id',
        'academic_map_id',
        'remark_role',
        'remark_person',
        'remark_note',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the student that owns the remark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic mapping for the remark.
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
     * Scope a query to filter by remark role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('remark_role', $role);
    }

    /**
     * Scope a query to filter by remark person.
     */
    public function scopeByPerson($query, $person)
    {
        return $query->where('remark_person', 'like', '%' . $person . '%');
    }

    /**
     * Get available remark roles.
     */
    public static function getRemarkRoles()
    {
        return [
            'Director',
            'Principal',
            'Vice Principal',
            'Coordinator',
            'SSRO',
            'HoD',
            'Teacher',
            'Admin',
            'others'
        ];
    }
}