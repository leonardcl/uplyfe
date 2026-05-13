<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    protected $fillable = [
        'user_id', 'span', 'target_calories', 'diet',
        'request_payload', 'payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'payload' => 'array',
    ];
}
