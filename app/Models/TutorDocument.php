<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorDocument extends Model
{
    protected $fillable = [
        'tutor_profile_id', 'type', 'file_path',
        'original_name', 'status', 'admin_note', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function tutorProfile() { return $this->belongsTo(TutorProfile::class); }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
