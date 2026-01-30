<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCommissionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'notes',
        'paid_by',
    ];

    /**
     * Get the admin who made the payment.
     */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
