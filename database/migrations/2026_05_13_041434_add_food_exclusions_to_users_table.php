<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Things the user has told us they can't / won't eat (e.g. "fish",
            // "shellfish", "peanuts"). Fed into recipe generation as
            // allergies/exclusions and surfaced to the chat in user_context
            // so the assistant remembers across conversations.
            $table->json('food_exclusions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('food_exclusions');
        });
    }
};
