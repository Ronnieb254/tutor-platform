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
        'is_auto_created',
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
            // Set defaults only for columns that exist in the DB
            $clinic->meeting_room = $clinic->meeting_room ?? ('MwalimuClinic-' . Str::uuid());
            $clinic->max_students = $clinic->max_students ?? 30;

            if (\Schema::hasColumn('examiner_clinics', 'batch_number')) {
                $clinic->batch_number = $clinic->batch_number ?? 1;
            }
            if (\Schema::hasColumn('examiner_clinics', 'session_code')) {
                $clinic->session_code = $clinic->session_code ?? ('CLINIC-' . strtoupper(Str::random(6)));
            }
        });

        // Auto-create overflow clinic when reaching 30 students
        static::updated(function ($clinic) {
            try {
                if ($clinic->isFull() && !$clinic->hasOverflowClinic()) {
                    $clinic->createOverflowClinic();
                }
            } catch (\Exception $e) {
                \Log::warning('Could not auto-create overflow clinic: ' . $e->getMessage());
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
     * Check if this clinic has an overflow clinic already created
     */
    public function hasOverflowClinic(): bool
    {
        if (!\Schema::hasColumn('examiner_clinics', 'parent_clinic_id')) {
            return false;
        }
        return self::where('parent_clinic_id', $this->id)
            ->where('is_auto_created', true)
            ->exists();
    }

    /**
     * Create an overflow clinic automatically when this one fills up (30 students)
     * New clinic starts 30 minutes after the previous one ends
     */
    public function createOverflowClinic(): ?self
    {
        // Don't create if this is already an overflow clinic
        if ($this->is_auto_created) {
            return null;
        }

        // Don't create if overflow already exists
        if ($this->hasOverflowClinic()) {
            return null;
        }

        // Calculate next clinic start time: previous end + 30 minutes
        $previousEnd = $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
        $nextStart = $previousEnd->addMinutes(30); // 30-minute break

        // Count existing batches to determine the next batch number
        $nextBatch = 1;
        if (\Schema::hasColumn('examiner_clinics', 'batch_number')) {
            $nextBatch = self::where(function ($q) {
                $q->where('id', $this->id)
                  ->orWhere('parent_clinic_id', $this->id);
            })->max('batch_number') + 1;
        }

        $overflowData = [
            'tutor_id'         => $this->tutor_id,
            'subject_id'       => $this->subject_id,
            'title'            => $this->title . ' – Batch ' . $nextBatch,
            'description'      => $this->description,
            'topics_covered'   => $this->topics_covered,
            'scheduled_at'     => $nextStart,
            'duration_minutes' => $this->duration_minutes,
            'price'            => $this->price,
            'max_students'     => 30,
            'status'           => 'scheduled',
        ];

        // Only set columns that exist in the DB
        if (\Schema::hasColumn('examiner_clinics', 'paper_type')) {
            $overflowData['paper_type'] = $this->paper_type;
        }
        if (\Schema::hasColumn('examiner_clinics', 'parent_clinic_id')) {
            $overflowData['parent_clinic_id'] = $this->id;
        }
        if (\Schema::hasColumn('examiner_clinics', 'batch_number')) {
            $overflowData['batch_number'] = $nextBatch;
        }
        if (\Schema::hasColumn('examiner_clinics', 'name')) {
            $overflowData['name'] = $this->title . ' – Batch ' . $nextBatch;
        }
        if (\Schema::hasColumn('examiner_clinics', 'created_by')) {
            $overflowData['created_by'] = $this->created_by;
        }
        if (\Schema::hasColumn('examiner_clinics', 'is_auto_created')) {
            $overflowData['is_auto_created'] = true;
        }

        $overflow = self::create($overflowData);

        \Log::info("Auto-created overflow clinic #{$overflow->id} for clinic #{$this->id}", [
            'original_start' => $this->scheduled_at,
            'overflow_start' => $nextStart,
            'batch_number'   => $nextBatch,
        ]);

        return $overflow;
    }

    /**
     * Get Jitsi join URL for this clinic
     */
    public function getJitsiJoinUrl(User $user): string
    {
        $jitsi = app(\App\Services\JitsiService::class);
        
        $isModerator = $user->id === $this->tutor_id || $user->isAdmin();
        
        $options = [
            'email' => $user->email,
            'recording' => $isModerator,
            'restrictGuests' => false, // Allow all enrolled students
        ];
        
        return $jitsi->getJoinUrl(
            $this->meeting_room,
            $user->name,
            $isModerator,
            $options
        );
    }

    /**
     * Check if user can join this clinic session
     */
    public function canJoin(User $user): bool
    {
        // Tutors and admins can always join
        if ($user->id === $this->tutor_id || $user->isAdmin()) {
            return true;
        }

        // Students must be enrolled
        if (!$this->isEnrolledBy($user)) {
            return false;
        }

        // Check if clinic has started (allow joining 15 minutes before)
        $canJoinFrom = $this->scheduled_at->copy()->subMinutes(15);
        
        return now()->gte($canJoinFrom);
    }

    /**
     * Auto-create a new session for the same subject + paper when this one fills up.
     * The new session is scheduled one week later at the same time.
     * 
     * @deprecated Use createOverflowClinic() instead
     */
    public function autoCreateNextSession(): ?self
    {
        return $this->createOverflowClinic();
    }

    /**
     * Find the next available (non-full) session for a given subject + paper type.
     */
    public static function findNextAvailable(?int $subjectId, ?string $paperType = null): ?self
    {
        $query = self::upcoming()
            ->where('subject_id', $subjectId);

        // Only filter by paper_type if the column exists and a value is provided
        if ($paperType && \Schema::hasColumn('examiner_clinics', 'paper_type')) {
            $query->where('paper_type', $paperType);
        }

        return $query->get()
            ->filter(fn($c) => !$c->isFull())
            ->sortBy('scheduled_at')
            ->first();
    }
}
