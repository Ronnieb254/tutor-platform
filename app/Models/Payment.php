<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'clinic_id', 'course_id', 'user_id', 'amount', 'currency',
        'payment_type', 'tutor_amount', 'platform_amount', 'commission_rate',
        'mpesa_reference', 'checkout_request_id', 'merchant_request_id',
        'mpesa_receipt', 'payer_phone',
        'status', 'failure_reason', 'metadata', 'paid_at',
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
        if ($this->payment_type === 'clinic' || $this->payment_type === 'group_session') {
            $this->commission_rate = 75.00; // Platform takes 75%
            $this->tutor_amount    = round($this->amount * 0.25, 2);
            $this->platform_amount = round($this->amount * 0.75, 2);
        } else {
            // Normal 1-on-1 booking: 40% platform, 60% tutor
            $this->commission_rate = 40.00;
            $this->tutor_amount    = round($this->amount * 0.60, 2);
            $this->platform_amount = round($this->amount * 0.40, 2);
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
