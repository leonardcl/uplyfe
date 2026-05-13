<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExercisePlan extends Model
{
    protected $fillable = [
        'user_id', 'title', 'fitness_goals', 'available_days',
        'request_payload', 'payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'payload' => 'array',
    ];
}
