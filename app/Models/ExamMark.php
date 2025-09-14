<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    use HasFactory;

    protected $table = 'exam_marks';

    protected $fillable = [
        'student_id',
        'academic_map_id',
        'exam_id',
        'subject_id',
        'marks_obtained',
        'grade',
        'grade_point',
        'remarks'
    ];

    protected $casts = [
        'grade_point' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the exam mark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic mapping that owns the exam mark.
     */
    public function academicMapping()
    {
        return $this->belongsTo(StudentAcademicMapping::class, 'academic_map_id');
    }

    /**
     * Get the exam that owns the exam mark.
     */
    public function exam()
    {
        return $this->belongsTo(ExamSetting::class, 'exam_id');
    }

    /**
     * Get the subject that owns the exam mark.
     */
    public function subject()
    {
        return $this->belongsTo(GradeStreamSubject::class, 'subject_id');
    }

    /**
     * Scope a query to include marks for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to include marks for a specific academic mapping.
     */
    public function scopeForAcademicMapping($query, $academicMapId)
    {
        return $query->where('academic_map_id', $academicMapId);
    }

    /**
     * Scope a query to include marks for a specific exam.
     */
    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    /**
     * Scope a query to include marks for a specific subject.
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope a query to include marks for a specific student and exam.
     */
    public function scopeForStudentAndExam($query, $studentId, $examId)
    {
        return $query->where('student_id', $studentId)->where('exam_id', $examId);
    }

    /**
     * Check if the marks data is valid for the academic context.
     */
    public function isValidAcademicContext()
    {
        // Check if academic mapping belongs to student
        if ($this->academicMapping->student_id != $this->student_id) {
            return false;
        }

        // Check if exam belongs to same academic year as academic mapping
        if ($this->exam->academic_year_id != $this->academicMapping->academic_year_id) {
            return false;
        }

        // Check if subject is available for student's grade and stream
        $subject = $this->subject;
        if ($subject->grade_id != $this->academicMapping->grade_id) {
            return false;
        }

        if ($subject->stream_id && $subject->stream_id != $this->academicMapping->stream_id) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted marks information.
     */
    public function getFormattedMarksAttribute()
    {
        $info = [];
        
        if ($this->marks_obtained) {
            $info[] = "Marks: {$this->marks_obtained}";
        }
        
        if ($this->grade) {
            $info[] = "Grade: {$this->grade}";
        }
        
        if ($this->grade_point) {
            $info[] = "GP: {$this->grade_point}";
        }

        return implode(' | ', $info);
    }
}