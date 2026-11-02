<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'institution',
        'course',
        'year_of_study',
        'startup_name',
        'sector',
        'stage',
        'problem',
        'solution',
        'team_size',
        'link',
        'mentor_areas',
        'goal',
        'commitment',
        'heard_about',
        'consent',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'mentor_areas' => 'array',
            'consent' => 'boolean',
        ];
    }
}
