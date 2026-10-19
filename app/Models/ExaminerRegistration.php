<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExaminerRegistration extends Model
{
    protected $fillable = [
        'tutor_id',
        'status',
        'rejection_reason',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
