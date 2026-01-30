<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'phone',
        'hospital',
        'position',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the role of the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        // Use loaded relationship if available to avoid extra query
        if ($this->relationLoaded('role')) {
            return $this->role && $this->role->name === $roleName;
        }
        return $this->role()->where('name', $roleName)->exists();
    }

    /**
     * Scope to filter users by role.
     */
    public function scopeRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is doctor.
     */
    public function isDoctor()
    {
        return $this->hasRole('doctor');
    }

    /**
     * Check if user is registrar.
     */
    public function isRegistrar()
    {
        return $this->hasRole('registrar');
    }

    /**
     * Get patients registered by this user (doctor).
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'registered_by');
    }

    /**
     * Get referrals created by this user (doctor).
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'doctor_id');
    }

    /**
     * Calculate doctor's balance from approved referrals with discount support.
     */
    public function calculateBalance()
    {
        if (!$this->isDoctor()) {
            return 0;
        }

        // Use new doctor_commission field if available
        return $this->referrals()
            ->where('is_approved', true)
            ->where('is_priced', true)
            ->sum('doctor_commission');
    }

    /**
     * Get detailed balance breakdown with discount information.
     */
    public function getBalanceDetails()
    {
        if (!$this->isDoctor()) {
            return [];
        }

        $details = [];
        $approvedReferrals = $this->referrals()
            ->where('is_approved', true)
            ->where('is_priced', true)
            ->with(['analyses', 'patient'])
            ->get();

        foreach ($approvedReferrals as $referral) {
            $details[] = [
                'referral_id' => $referral->id,
                'patient_name' => $referral->patient->full_name,
                'original_price' => $referral->total_price,
                'discount_type' => $referral->discount_type,
                'discount_value' => $referral->discount_value,
                'final_price' => $referral->final_price,
                'commission_amount' => $referral->doctor_commission,
                'is_free' => ($referral->discount_type === 'percentage' && $referral->discount_value == 100),
                'date' => $referral->approved_at,
                'analyses_count' => $referral->analyses->count(),
            ];
        }

        return $details;
    }

    /**
     * Get payments received by doctor.
     */
    public function paymentsReceived()
    {
        return $this->hasMany(Payment::class, 'doctor_id');
    }

    /**
     * Get payments made by admin.
     */
    public function paymentsMade()
    {
        return $this->hasMany(Payment::class, 'admin_id');
    }

    /**
     * Get user notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get total paid amount (for doctors).
     */
    public function getTotalPaidAmount()
    {
        return $this->paymentsReceived()->sum('amount');
    }

    /**
     * Get balance (alias for calculateBalance).
     */
    public function getBalance()
    {
        return $this->calculateBalance();
    }

    /**
     * Get remaining balance (total commission - total paid).
     */
    public function getRemainingBalance()
    {
        return $this->getBalance() - $this->getTotalPaidAmount();
    }

    /**
     * Get sent messages.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get received messages.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread messages count.
     */
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->unread()->count();
    }
}
