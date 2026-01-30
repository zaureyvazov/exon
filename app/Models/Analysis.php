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

    /**
     * Get most popular analyses for a specific doctor
     */
    public static function getPopularForDoctor($doctorId)
    {
        return self::select('analyses.*', \DB::raw('COUNT(referral_analyses.analysis_id) as usage_count'))
            ->join('referral_analyses', 'analyses.id', '=', 'referral_analyses.analysis_id')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->where('referrals.doctor_id', $doctorId)
            ->where('analyses.is_active', true)
            ->groupBy('analyses.id')
            ->orderBy('usage_count', 'desc')
            ->get();
    }
}
