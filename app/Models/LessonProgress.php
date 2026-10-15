<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $fillable = ['student_id', 'lesson_id', 'completed', 'watch_seconds'];

    protected function casts(): array
    {
        return ['completed' => 'boolean'];
    }

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function lesson()  { return $this->belongsTo(CourseLesson::class, 'lesson_id'); }
}
