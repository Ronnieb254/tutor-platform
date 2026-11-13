<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSessionSuggestion extends Model
{
    protected $table = 'group_session_suggestions';

    protected $fillable = [
        'user_id',
        'email',
        'topic',
        'description',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the suggestion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
