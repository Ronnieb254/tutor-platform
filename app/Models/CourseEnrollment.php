<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = ['student_id', 'course_id', 'payment_id', 'progress_percent', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function course()  { return $this->belongsTo(Course::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
}
