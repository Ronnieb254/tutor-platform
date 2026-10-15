<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'tutor_id', 'category_id', 'title', 'slug', 'description',
        'thumbnail', 'price', 'is_free', 'is_published',
        'total_lessons', 'total_duration_seconds', 'level',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'is_free'      => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function tutor()       { return $this->belongsTo(User::class, 'tutor_id'); }
    public function category()    { return $this->belongsTo(Category::class); }
    public function lessons()     { return $this->hasMany(CourseLesson::class)->orderBy('sort_order'); }
    public function enrollments() { return $this->hasMany(CourseEnrollment::class); }

    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()->where('student_id', $user->id)->exists();
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
}
