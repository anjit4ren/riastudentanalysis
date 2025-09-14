<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYearSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'academicyear_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'running',
        'starting_date',
        'ending_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'running' => 'boolean',
        'starting_date' => 'date',
        'ending_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include running academic settings.
     */
    public function scopeRunning($query)
    {
        return $query->where('running', true);
    }

    /**
     * Scope a query to only include non-running academic settings.
     */
    public function scopeNotRunning($query)
    {
        return $query->where('running', false);
    }

    /**
     * Check if the academic setting is currently active based on dates.
     */
    public function isCurrentlyActive(): bool
    {
        $now = now();
        return $this->running && 
               $this->starting_date <= $now && 
               $this->ending_date >= $now;
    }

    /**
     * Get the duration in days.
     */
    public function getDurationInDays(): int
    {
        return $this->starting_date->diffInDays($this->ending_date);
    }

    /**
     * Scope a query to only include active grade settings.
     */
    public function scopeActive($query)
    {
        return $query->where('running', true);
    }
}