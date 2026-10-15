<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['booking_id', 'student_id', 'tutor_id', 'rating', 'comment', 'is_visible'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function tutor()   { return $this->belongsTo(User::class, 'tutor_id'); }

    public function scopeVisible($q) { return $q->where('is_visible', true); }
}
