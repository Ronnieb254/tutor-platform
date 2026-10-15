<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExaminerClinic extends Model
{
    protected $fillable = [
        'tutor_id','subject_id','title','description','topics_covered',
        'scheduled_at','duration_minutes','price','max_students','status','meeting_room',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'  => 'datetime',
            'topics_covered'=> 'array',
            'price'         => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($clinic) {
            $clinic->meeting_room = $clinic->meeting_room ?? 'mwalimu-clinic-' . Str::uuid();
        });
    }

    public function tutor()       { return $this->belongsTo(User::class, 'tutor_id'); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function enrollments() { return $this->hasMany(ClinicEnrollment::class, 'clinic_id'); }

    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()->where('student_id', $user->id)->exists();
    }

    public function spotsLeft(): int
    {
        return max(0, $this->max_students - $this->enrollments()->count());
    }

    public function scopeUpcoming($q)
    {
        return $q->where('scheduled_at', '>', now())->where('status', 'scheduled');
    }
}
