<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeStreamSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grade_stream_subjects';

    protected $fillable = [
        'subject_name',
        'grade_id',
        'stream_id',
        'order',
        'is_active'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the grade that owns the subject.
     */
    public function grade()
    {
        return $this->belongsTo(GradeSetting::class, 'grade_id');
    }

    /**
     * Get the stream that owns the subject.
     */
    public function stream()
    {
        return $this->belongsTo(StreamSetting::class, 'stream_id');
    }

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the order field.
     */
    public function scopeOrdered($query, $direction = 'asc')
    {
        return $query->orderBy('order', $direction)
                    ->orderBy('subject_name', $direction);
    }

    /**
     * Scope a query to include subjects for a specific grade.
     */
    public function scopeForGrade($query, $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    /**
     * Scope a query to include subjects for a specific stream.
     */
    public function scopeForStream($query, $streamId)
    {
        return $query->where('stream_id', $streamId);
    }

    /**
     * Scope a query to include subjects for a specific grade and stream.
     */
    public function scopeForGradeAndStream($query, $gradeId, $streamId)
    {
        return $query->where('grade_id', $gradeId)
                    ->where('stream_id', $streamId);
    }

    /**
     * Scope a query to include common subjects (without specific stream).
     */
    public function scopeCommonSubjects($query)
    {
        return $query->whereNull('stream_id');
    }

    /**
     * Get the next available order value for a grade and stream.
     */
    public static function getNextOrder($gradeId, $streamId = null)
    {
        return self::where('grade_id', $gradeId)
                  ->where('stream_id', $streamId)
                  ->max('order') + 1;
    }

    /**
     * Get the full subject name with grade and stream context.
     */
    public function getFullSubjectNameAttribute()
    {
        $name = $this->subject_name;
        
        if ($this->grade) {
            $name .= " ({$this->grade->name}";
            
            if ($this->stream) {
                $name .= " - {$this->stream->name}";
            }
            
            $name .= ")";
        }
        
        return $name;
    }

    /**
     * Check if the subject is a common subject (not stream-specific).
     */
    public function getIsCommonSubjectAttribute()
    {
        return is_null($this->stream_id);
    }
}