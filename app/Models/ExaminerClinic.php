<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExaminerClinic extends Model
{
    protected $fillable = [
        'tutor_id','subject_id','paper_type','parent_clinic_id','batch_number',
        'title','name','description','topics_covered',
        'scheduled_at','duration_minutes','price','max_students','status','meeting_room',
        'created_by','session_code','recording_url',
        'bbb_meeting_id','bbb_attendee_pw','bbb_moderator_pw',
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
            $clinic->batch_number = $clinic->batch_number ?? 1;
        });

        // Auto-create the next session when a clinic becomes full
        static::updated(function ($clinic) {
            if ($clinic->isFull() && $clinic->parent_clinic_id === null) {
                $clinic->autoCreateNextSession();
            }
        });
    }

    public function tutor()       { return $this->belongsTo(User::class, 'tutor_id'); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function enrollments() { return $this->hasMany(ClinicEnrollment::class, 'clinic_id'); }
    public function parentClinic() { return $this->belongsTo(ExaminerClinic::class, 'parent_clinic_id'); }
    public function childSessions() { return $this->hasMany(ExaminerClinic::class, 'parent_clinic_id'); }

    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()->where('student_id', $user->id)->exists();
    }

    public function spotsLeft(): int
    {
        return max(0, $this->max_students - $this->enrollments()->count());
    }

    public function enrolledCount(): int
    {
        return $this->enrollments()->count();
    }

    public function isFull(): bool
    {
        return $this->enrolledCount() >= $this->max_students;
    }

    public function scopeUpcoming($q)
    {
        return $q->where('scheduled_at', '>', now())->where('status', 'scheduled');
    }

    public function scopeNotFull($q)
    {
        // Clinics that still have spots available
        return $q->whereDoesntHave('enrollments', function ($sub) {
            $sub->selectRaw('clinic_id, count(*) as cnt')
                ->groupBy('clinic_id')
                ->havingRaw('count(*) >= max_students');
        });
    }

    /**
     * Get the display title with paper type included.
     */
    public function paperLabel(): string
    {
        if ($this->paper_type) {
            return $this->subject?->name . ' ' . $this->paper_type;
        }
        return $this->title;
    }

    /**
     * Auto-create a new session for the same subject + paper when this one fills up.
     * The new session is scheduled one week later at the same time.
     */
    public function autoCreateNextSession(): ?self
    {
        // Don't create duplicates if a non-full child already exists
        $existingChild = self::where('parent_clinic_id', $this->id)
            ->where('status', 'scheduled')
            ->whereDoesntHave('enrollments', function ($q) {
                $q->havingRaw('count(*) >= ' . $this->max_students);
            })
            ->exists();

        if ($existingChild) {
            return null;
        }

        // Count existing batches to determine the next batch number
        $nextBatch = self::where(function ($q) {
            $q->where('id', $this->id)->orWhere('parent_clinic_id', $this->id);
        })->max('batch_number') + 1;

        return self::create([
            'tutor_id'        => $this->tutor_id,
            'subject_id'       => $this->subject_id,
            'paper_type'       => $this->paper_type,
            'parent_clinic_id' => $this->id,
            'batch_number'    => $nextBatch,
            'title'           => $this->paperLabel() . ' – Session ' . $nextBatch,
            'description'      => $this->description,
            'topics_covered'  => $this->topics_covered,
            'scheduled_at'    => $this->scheduled_at->copy()->addWeek(),
            'duration_minutes' => $this->duration_minutes,
            'price'           => $this->price,
            'max_students'    => $this->max_students,
            'status'          => 'scheduled',
        ]);
    }

    /**
     * Find the next available (non-full) session for a given subject + paper type.
     */
    public static function findNextAvailable(?int $subjectId, ?string $paperType = null): ?self
    {
        return self::upcoming()
            ->where('subject_id', $subjectId)
            ->when($paperType, fn($q) => $q->where('paper_type', $paperType))
            ->whereDoesntHave('enrollments', function ($q) {
                // Only exclude clinics that are actually full
            })
            ->get()
            ->filter(fn($c) => !$c->isFull())
            ->sortBy('scheduled_at')
            ->first();
    }
}
