<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TutorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bio', 'hourly_rate', 'experience_years',
        'languages', 'country', 'city', 'is_approved', 'approved_at',
        'rating_avg', 'total_reviews', 'total_sessions',
        'video_intro', 'certificates', 'subscription_plan', 'subscription_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'languages'               => 'array',
            'certificates'            => 'array',
            'is_approved'             => 'boolean',
            'approved_at'             => 'datetime',
            'subscription_expires_at' => 'datetime',
            'hourly_rate'             => 'decimal:2',
            'rating_avg'              => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'tutor_subjects');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function documents()
    {
        return $this->hasMany(TutorDocument::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tutor_id', 'user_id');
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    // Fixed session pricing per Mwalimu
    public static function sessionPrices(): array
    {
        return [
            '20_individual' => ['name' => 'Quick Help',       'duration' => 20, 'price' => 300, 'icon' => '⚡', 'desc' => 'Rapid revision or a quick question', 'type' => 'individual'],
            '40_individual' => ['name' => 'Guided Practice',  'duration' => 40, 'price' => 500, 'icon' => '🎯', 'desc' => 'Full structured lesson with Q&A', 'type' => 'individual'],
            '60_individual' => ['name' => 'Deep Dive',        'duration' => 60, 'price' => 700, 'icon' => '🏆', 'desc' => 'Maximum learning, best value', 'type' => 'individual'],
            '60_group'      => ['name' => 'Group Session',    'duration' => 60, 'price' => 500, 'icon' => '👥', 'desc' => 'Learn together, share the cost', 'type' => 'group'],
        ];
    }

    /**
     * Check if a student qualifies for a free first 15-min session with this tutor.
     * First time = no confirmed/completed booking between student and this tutor.
     */
    public static function isFreeFirstSession(int $studentId, int $tutorUserId): bool
    {
        return !\App\Models\Booking::where('student_id', $studentId)
            ->where('tutor_id', $tutorUserId)
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->exists();
    }

    /**
     * Get the price for a given duration, factoring in the free first 15-min offer.
     */
    public function getPriceForStudent(int $minutes, ?int $studentId = null): float
    {
        if ($minutes === 15 && $studentId) {
            if (self::isFreeFirstSession($studentId, $this->user_id)) {
                return 0.00; // FREE first 15-min session
            }
        }
        return $this->getPriceForDuration($minutes);
    }

    /**
     * Get the price for a specific session duration and type.
     */
    public function getPriceForSession(int $minutes, string $type = 'individual'): float
    {
        $key = "{$minutes}_{$type}";
        return self::sessionPrices()[$key]['price'] ?? 300;
    }

    /**
     * Get the session name for a duration and type.
     */
    public function getSessionNameForSession(int $minutes, string $type = 'individual'): string
    {
        $key = "{$minutes}_{$type}";
        return self::sessionPrices()[$key]['name'] ?? 'Session';
    }

    public function getPriceForDuration(int $minutes): float
    {
        return $this->getPriceForSession($minutes, 'individual');
    }

    public function getSessionNameForDuration(int $minutes): string
    {
        return $this->getSessionNameForSession($minutes, 'individual');
    }

    public function getVerificationStatusAttribute(): string
    {
        $docs = $this->documents;
        
        // Required document types (4 documents now)
        $requiredTypes = ['national_id', 'tsc_certificate', 'degree_certificate', 'passport_photo'];
        
        // Check if any documents are uploaded
        if ($docs->isEmpty()) return 'no_documents';
        
        // Check if any document is rejected
        if ($docs->where('status', 'rejected')->count() > 0) return 'action_required';
        
        // Check if all 4 required documents are uploaded
        $uploadedTypes = $docs->pluck('type')->toArray();
        $missingDocs = array_diff($requiredTypes, $uploadedTypes);
        
        if (count($missingDocs) > 0) return 'no_documents'; // Still need to upload all 4
        
        // All 4 docs uploaded, check if any are pending
        if ($docs->where('status', 'pending')->count() > 0) return 'under_review';
        
        // All approved
        if ($this->is_approved) return 'approved';
        
        return 'under_review';
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->user->name ?? 'tutor-'.$this->id);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('bio', 'like', "%{$term}%")
              ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"))
              ->orWhereHas('subjects', fn($s) => $s->where('name', 'like', "%{$term}%"));
        });
    }
}
