<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'student_id', 'tutor_id', 'subject_id',
        'scheduled_at', 'duration_minutes', 'status',
        'meeting_url', 'meeting_room', 'meeting_password', 'moderator_password',
        'total_amount', 'platform_fee', 'tutor_earning',
        'student_notes', 'cancellation_reason', 'cancelled_at', 'completed_at',
        'student_joined', 'tutor_joined',
        'session_code_tutor', 'session_code_student',
        'booking_for', 'child_profile_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'   => 'datetime',
            'cancelled_at'   => 'datetime',
            'completed_at'   => 'datetime',
            'total_amount'   => 'decimal:2',
            'platform_fee'   => 'decimal:2',
            'tutor_earning'  => 'decimal:2',
            'student_joined' => 'boolean',
            'tutor_joined'   => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($booking) {
            $booking->uuid = $booking->uuid ?? (string) Str::uuid();
        });
    }

    public function student()    { return $this->belongsTo(User::class, 'student_id'); }
    public function tutor()      { return $this->belongsTo(User::class, 'tutor_id'); }
    public function subject()    { return $this->belongsTo(Subject::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function review()     { return $this->hasOne(Review::class); }
    public function messages()   { return $this->hasMany(Message::class); }
    public function childProfile() { return $this->belongsTo(ChildProfile::class); }

    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture() && in_array($this->status, ['pending', 'confirmed']);
    }

    public function canJoin(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        $opensAt  = $this->scheduled_at->copy()->subMinutes(60);
        $closesAt = $this->scheduled_at->copy()->addMinutes($this->duration_minutes + 30);

        return now()->between($opensAt, $closesAt);
    }

    public function scopeHistory($q)
    {
        return $q->where(function ($inner) {
            $inner->whereIn('status', ['completed', 'cancelled', 'no_show'])
                ->orWhere(function ($past) {
                    $past->where('scheduled_at', '<=', now())
                        ->whereNotIn('status', ['pending']);
                });
        });
    }

    public function formattedDuration(): string
    {
        return $this->duration_minutes . ' min';
    }

    public function getRouteKeyName(): string { return 'uuid'; }

    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeConfirmed($q) { return $q->where('status', 'confirmed'); }
    public function scopeCompleted($q) { return $q->where('status', 'completed'); }
    public function scopeUpcoming($q)  { return $q->whereIn('status', ['pending','confirmed'])->where('scheduled_at', '>', now()); }
}
