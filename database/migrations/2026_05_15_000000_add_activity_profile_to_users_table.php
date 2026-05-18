<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('exercise_preference')->nullable()->after('calorie_goal');
            $table->json('equipment_available')->nullable()->after('exercise_preference');
            $table->string('available_days')->nullable()->after('equipment_available');
            $table->string('time_available')->nullable()->after('available_days');
            $table->string('fitness_goals')->nullable()->after('time_available');
            $table->json('body_focus')->nullable()->after('fitness_goals');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['exercise_preference', 'equipment_available', 'available_days', 'time_available', 'fitness_goals', 'body_focus']);
        });
    }
};
