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
        Schema::create('health_reports', function (Blueprint $table) {
            $table->id();
            // user_id is nullable so anonymous (logged-out) uploads still save —
            // history view is gated by login but the upload itself isn't.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Original file metadata
            $table->string('original_filename')->nullable();
            $table->unsignedInteger('original_size_bytes')->nullable();

            // Quick-look fields for list views (no JSON parsing needed):
            $table->string('overall_severity')->nullable();   // normal | borderline | abnormal | critical
            $table->unsignedSmallInteger('biomarker_count')->default(0);
            $table->unsignedSmallInteger('abnormal_count')->default(0);
            $table->unsignedSmallInteger('critical_count')->default(0);
            $table->text('summary')->nullable();

            // Full FinalReport JSON — render anything the UI needs without re-running the pipeline.
            $table->json('payload');

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_reports');
    }
};
