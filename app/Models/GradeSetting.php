<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeSetting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grade_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'active_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active_status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Check if the grade setting can be deleted.
     * Prevents deletion if linked to other models.
     */
    public function canDelete(): bool
    {
        // Add relationships here that should prevent deletion
        // Example: return $this->students()->count() === 0;
        return true; // Modify this based on your relationships
    }

    /**
     * Scope a query to only include active grade settings.
     */
    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    /**
     * Scope a query to only include inactive grade settings.
     */
    public function scopeInactive($query)
    {
        return $query->where('active_status', false);
    }

    /**
     * Scope a query to only include trashed (soft deleted) grade settings.
     */
    public function scopeTrashed($query)
    {
        return $query->onlyTrashed();
    }

    /**
     * Scope a query to include both trashed and non-trashed grade settings.
     */
    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }
}