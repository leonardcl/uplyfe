<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikedMeal extends Model
{
    protected $fillable = [
        'user_id', 'meal_plan_id', 'day_key', 'meal_type', 'title', 'snapshot',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];
}
