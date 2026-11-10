<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSessionSuggestion extends Model
{
    protected $fillable = [
        'group_session_id',
        'subject_id',
        'custom_subject',
        'area',
        'votes_count',
        'created_by',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function groupSession()
    {
        return $this->belongsTo(ExaminerClinic::class, 'group_session_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(GroupSessionSuggestionVote::class, 'suggestion_id');
    }
}
