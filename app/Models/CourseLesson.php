<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'description',
        'video_url', 'duration_seconds', 'sort_order', 'is_free_preview',
    ];

    protected function casts(): array
    {
        return ['is_free_preview' => 'boolean'];
    }

    public function course()   { return $this->belongsTo(Course::class); }
    public function progress() { return $this->hasMany(LessonProgress::class, 'lesson_id'); }

    public function formattedDuration(): string
    {
        $m = intdiv($this->duration_seconds, 60);
        $s = $this->duration_seconds % 60;
        return sprintf('%d:%02d', $m, $s);
    }
}
