<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'identifier',
        'type',
        'otp',
        'verified',
        'attempts',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified'   => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function isMaxAttemptsReached(): bool
    {
        return $this->attempts >= 5;
    }
}
