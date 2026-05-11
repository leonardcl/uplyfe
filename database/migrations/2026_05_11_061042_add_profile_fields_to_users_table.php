<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 32)->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone_number');
            $table->string('gender', 16)->nullable()->after('date_of_birth');
            $table->string('profile_photo')->nullable()->after('gender');
            $table->json('dietary_preferences')->nullable()->after('age');
            $table->json('notification_preferences')->nullable()->after('dietary_preferences');
            $table->timestamp('password_changed_at')->nullable()->after('notification_preferences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'date_of_birth',
                'gender',
                'profile_photo',
                'dietary_preferences',
                'notification_preferences',
                'password_changed_at',
            ]);
        });
    }
};
