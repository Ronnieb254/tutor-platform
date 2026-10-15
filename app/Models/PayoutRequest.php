<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $fillable = [
        'tutor_id', 'amount', 'status',
        'bank_name', 'account_number', 'account_name',
        'admin_note', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function tutor() { return $this->belongsTo(User::class, 'tutor_id'); }
}
