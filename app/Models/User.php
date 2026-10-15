<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar',
        'provider', 'provider_id', 'phone', 'timezone', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // Relationships
    public function tutorProfile()
    {
        return $this->hasOne(TutorProfile::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function bookingsAsStudent()
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    public function bookingsAsTutor()
    {
        return $this->hasMany(Booking::class, 'tutor_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'student_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'tutor_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function wishlist()
    {
        return $this->belongsToMany(TutorProfile::class, 'wishlists');
    }

    public function childProfiles()
    {
        return $this->hasMany(ChildProfile::class, 'parent_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'tutor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'student_id');
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'tutor_id');
    }

    // Helpers
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isTutor(): bool   { return $this->role === 'tutor'; }
    public function isStudent(): bool { return $this->role === 'student'; }
    public function isParent(): bool  { return $this->role === 'parent'; }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6D28D9&color=fff';
    }
}
