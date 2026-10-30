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
        'parent_id', 'age', 'grade_level',
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

    // New: Children as Users
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id')->where('role', 'student');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
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

    public function notificationPreferences()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function twoFactorAuth()
    {
        return $this->hasOne(TwoFactorAuth::class);
    }

    public function passwordHistory()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    // Helpers
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isTutor(): bool   { return $this->role === 'tutor'; }
    public function isStudent(): bool { return $this->role === 'student'; }
    public function isParent(): bool  { return $this->role === 'parent'; }

    public function has2FAEnabled(): bool
    {
        return $this->twoFactorAuth && $this->twoFactorAuth->enabled_at !== null;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0A64BC&color=fff';
    }
}
