<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'commission_percentage',
        'discount_commission_rate',
        'is_active',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'discount_commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category of this analysis
     */
    public function category()
    {
        return $this->belongsTo(AnalysisCategory::class, 'category_id');
    }

    /**
     * Check if analysis can be deleted
     */
    public function canBeDeleted()
    {
        return $this->referrals()->count() === 0;
    }

    /**
     * Scope for active analyses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the referrals that include this analysis.
     */
    public function referrals()
    {
        return $this->belongsToMany(Referral::class, 'referral_analyses');
    }
}
