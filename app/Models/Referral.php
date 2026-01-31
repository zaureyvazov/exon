<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'status',
        'is_approved',
        'approved_at',
        'approved_by',
        'notes',
        'sent_at',
        'discount_type',
        'discount_value',
        'final_price',
        'doctor_commission',
        'total_program_commission',
        'is_priced',
        'discount_reason',
        'is_cancelled',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_approved' => 'boolean',
        'is_priced' => 'boolean',
        'is_cancelled' => 'boolean',
        'discount_value' => 'decimal:2',
        'final_price' => 'decimal:2',
        'doctor_commission' => 'decimal:2',
        'total_program_commission' => 'decimal:2',
    ];

    /**
     * Get the patient for the referral.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor who created the referral.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the analyses for the referral with pivot data.
     */
    public function analyses()
    {
        return $this->belongsToMany(Analysis::class, 'referral_analyses')
            ->withPivot('analysis_price', 'commission_percentage', 'discount_commission_rate', 'is_cancelled', 'cancellation_reason');
    }

    /**
     * Scope a query to only include pending referrals.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed referrals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the user who approved the referral.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include approved referrals.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include unapproved referrals.
     */
    public function scopeUnapproved($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope to exclude cancelled referrals (for doctor and registrar views).
     */
    public function scopeNotCancelled($query)
    {
        return $query->where('is_cancelled', false);
    }

    /**
     * Scope to only show cancelled referrals (for admin).
     */
    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    /**
     * Get the user who cancelled the referral.
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Calculate total price from analysis snapshots in pivot table (only non-cancelled).
     */
    public function getTotalPriceAttribute()
    {
        return $this->analyses->filter(function($analysis) {
            return !($analysis->pivot->is_cancelled ?? false);
        })->sum('pivot.analysis_price');
    }

    /**
     * Calculate total price with tax (price * 1.3) for registrar display.
     */
    public function getTotalPriceWithTaxAttribute()
    {
        // Sum all analyses price_with_tax (already rounded in database)
        $total = 0;
        foreach ($this->analyses as $analysis) {
            if (!$analysis->pivot->is_cancelled) {
                $total += $analysis->price_with_tax;
            }
        }
        return round($total, 2);
    }

    /**
     * Calculate final price with tax after discount (for registrar).
     */
    public function getFinalPriceWithTaxAttribute()
    {
        if ($this->discount_type === 'none' || !$this->final_price) {
            return $this->total_price_with_tax;
        }
        return round($this->final_price * 1.3, 2);
    }

    /**
     * Get discount display text.
     */
    public function getDiscountDisplayAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        } elseif ($this->discount_type === 'amount') {
            return number_format($this->discount_value, 2) . ' AZN';
        }
        return 'Yoxdur';
    }

    /**
     * Check if referral can be edited by doctor.
     * Editable if: not approved AND created within last 1 hour
     */
    public function canBeEditedByDoctor()
    {
        if ($this->is_approved) {
            return false; // Təsdiqlənmişləri dəyişdirmək olmaz
        }

        // 1 saat keçibsə redaktə edilə bilməz
        $oneHourAgo = now()->subHour();
        return $this->created_at->gt($oneHourAgo);
    }

    /**
     * Check if referral can be edited by registrar.
     * Editable if: not approved (registrar can edit anytime before approval)
     */
    public function canBeEditedByRegistrar()
    {
        return !$this->is_approved;
    }

    /**
     * Get remaining time to edit (in minutes)
     */
    public function getRemainingEditTimeAttribute()
    {
        if ($this->is_approved) {
            return 0;
        }

        $oneHourAfterCreation = $this->created_at->addHour();
        $remainingMinutes = now()->diffInMinutes($oneHourAfterCreation, false);
        
        return max(0, $remainingMinutes);
    }
}
