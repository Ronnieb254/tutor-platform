<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'icon', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category()      { return $this->belongsTo(Category::class); }
    public function tutorProfiles() { return $this->belongsToMany(TutorProfile::class, 'tutor_subjects'); }
    public function bookings()      { return $this->hasMany(Booking::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
