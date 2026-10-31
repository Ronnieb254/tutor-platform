<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    const RESOURCE_TYPES = ['article', 'video', 'notes', 'past_paper'];

    protected $fillable = [
        'author_id', 'blog_category_id', 'title', 'slug',
        'excerpt', 'content', 'thumbnail',
        'is_published', 'published_at', 'meta_title', 'meta_description',
        'resource_type', 'file_path', 'file_size', 'subject_id',
        'grade_level', 'download_count', 'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_published'   => 'boolean',
            'is_approved'    => 'boolean',
            'download_count' => 'integer',
            'published_at'   => 'datetime',
        ];
    }

    public function author()   { return $this->belongsTo(User::class, 'author_id'); }
    public function category() { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function subject()  { return $this->belongsTo(Subject::class, 'subject_id'); }

    public function scopePublished($q)
    {
        return $q->where('is_published', true)
                 ->where('is_approved', true)
                 ->where('published_at', '<=', now());
    }

    public function scopeApproved($q)
    {
        return $q->where('is_approved', true);
    }
}
