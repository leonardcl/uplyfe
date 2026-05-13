<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liked_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('meal_plan_id')->nullable()->constrained('meal_plans')->nullOnDelete();
            $table->string('day_key')->nullable();   // e.g. "monday"
            $table->string('meal_type');             // breakfast | lunch | dinner | snack
            $table->string('title');
            $table->json('snapshot');                // full meal payload at like time
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->unique(['user_id', 'meal_plan_id', 'day_key', 'meal_type'], 'liked_meals_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liked_meals');
    }
};
