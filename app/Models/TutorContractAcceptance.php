<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorContractAcceptance extends Model
{
    protected $fillable = [
        'user_id',
        'contract_version',
        'contract_hash',
        'ip_address',
        'user_agent',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate SHA-256 hash of contract content
     */
    public static function generateContractHash(string $content): string
    {
        return hash('sha256', $content);
    }
}
