<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'clinic_id', 'user_id', 'amount', 'currency',
        'payment_type', 'tutor_amount', 'platform_amount', 'commission_rate',
        'paystack_reference', 'paystack_access_code',
        'status', 'metadata', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'   => 'decimal:2',
            'metadata' => 'array',
            'paid_at'  => 'datetime',
        ];
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function clinic() { return $this->belongsTo(ExaminerClinic::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function scopeSuccessful($q) { return $q->where('status', 'success'); }

    /**
     * Calculate and set commission amounts based on payment type.
     * 75% tutor / 25% platform for regular bookings
     * 40% tutor / 60% platform for exam clinics
     */
    public function calculateCommission(): void
    {
        if ($this->payment_type === 'clinic') {
            $this->commission_rate = 60.00; // Platform takes 60%
            $this->tutor_amount = $this->amount * 0.40;
            $this->platform_amount = $this->amount * 0.60;
        } else {
            $this->commission_rate = 25.00; // Platform takes 25%
            $this->tutor_amount = $this->amount * 0.75;
            $this->platform_amount = $this->amount * 0.25;
        }
    }

    /**
     * Boot method to auto-calculate commission on create.
     */
    protected static function booted(): void
    {
        static::creating(function ($payment) {
            // Only auto-calculate if commission columns exist and amounts aren't pre-set
            if ((is_null($payment->tutor_amount) || is_null($payment->platform_amount))) {
                try {
                    $payment->calculateCommission();
                } catch (\Exception $e) {
                    // Commission columns may not exist yet — skip silently
                    \Log::warning('Could not calculate commission: ' . $e->getMessage());
                }
            }
        });
    }
}
