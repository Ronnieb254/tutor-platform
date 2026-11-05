<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurriculumLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id', 'name', 'code', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function learnerProfiles()
    {
        return $this->hasMany(LearnerProfile::class, 'level_id');
    }
}
