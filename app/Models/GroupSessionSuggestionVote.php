<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSessionSuggestionVote extends Model
{
    protected $fillable = [
        'suggestion_id',
        'voter_id',
        'voter_ip',
        'browser_id',
    ];

    public function suggestion()
    {
        return $this->belongsTo(GroupSessionSuggestion::class, 'suggestion_id');
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }
}
