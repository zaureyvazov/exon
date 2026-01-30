<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_name',
        'surname',
        'serial_number',
        'phone',
        'registered_by',
    ];

    /**
     * Get the user that registered the patient.
     */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get the referrals for the patient.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Get full name of the patient.
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }
}
