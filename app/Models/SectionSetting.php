<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'section_settings';

    protected $fillable = [
        'name',
        'active_status',
    ];

    protected $casts = [
        'active_status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function canDelete(): bool
    {
        // Add relationship checks here
        // return $this->students()->count() === 0;
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active_status', false);
    }
}