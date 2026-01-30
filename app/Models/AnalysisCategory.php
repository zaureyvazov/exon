<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get analyses in this category
     */
    public function analyses()
    {
        return $this->hasMany(Analysis::class, 'category_id');
    }

    /**
     * Check if category can be deleted
     */
    public function canBeDeleted()
    {
        return $this->analyses()->count() === 0;
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
