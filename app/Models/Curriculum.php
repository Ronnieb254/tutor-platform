<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Curriculum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'track', 'slug', 'description', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function levels()
    {
        return $this->hasMany(CurriculumLevel::class)->orderBy('sort_order');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function learnerProfiles()
    {
        return $this->hasMany(LearnerProfile::class);
    }

    public function tutorProfiles()
    {
        return $this->belongsToMany(TutorProfile::class, 'tutor_curricula')
            ->withPivot(['verification_status', 'verified_at'])
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKenyan($query)
    {
        return $query->where('track', 'kenyan');
    }

    public function scopeInternational($query)
    {
        return $query->where('track', 'international');
    }
}
