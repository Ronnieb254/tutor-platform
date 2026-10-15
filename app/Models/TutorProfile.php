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
            15 => ['name' => 'Quick Help',       'price' => 300],
            30 => ['name' => 'Concept Check',    'price' => 500],
            45 => ['name' => 'Guided Practice',  'price' => 680],
            60 => ['name' => 'Deep Dive',         'price' => 800],
        ];
    }

    public function getPriceForDuration(int $minutes): float
    {
        return self::sessionPrices()[$minutes]['price'] ?? 300;
    }

    public function getSessionNameForDuration(int $minutes): string
    {
        return self::sessionPrices()[$minutes]['name'] ?? 'Session';
    }

    public function getVerificationStatusAttribute(): string
    {
        $docs = $this->documents;
        if ($docs->isEmpty()) return 'no_documents';
        if ($docs->where('status', 'rejected')->count() > 0) return 'action_required';
        if ($docs->where('status', 'pending')->count() > 0) return 'under_review';
        if ($this->is_approved) return 'approved';
        return 'under_review';
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
