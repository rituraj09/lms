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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. "ASS_0001"
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('question_set_id')->constrained()->restrictOnDelete();

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable();         // started_at + timer
            $table->unsignedSmallInteger('time_taken')->nullable(); // seconds actually taken

            // Scores
            $table->decimal('raw_score', 8, 2)->default(0);     // sum of earned scores
            $table->decimal('max_score', 8, 2)->default(0);     // sum of max possible scores
            $table->decimal('percentage', 5, 2)->nullable();     // raw_score / max_score * 100
            $table->decimal('iq_score', 6, 2)->nullable();       // normalized IQ/EQ/LQ score
            $table->decimal('eq_score', 6, 2)->nullable();
            $table->decimal('lq_score', 6, 2)->nullable();

            // Per-skill scores stored as JSON for detailed reporting
            // e.g. {"Attention": 85, "Memory": 72, "Logical Thinking": 90}
            $table->json('skill_scores')->nullable();

            $table->enum('result', ['pass', 'fail', 'pending_review'])->nullable();
            $table->enum('status', [
                'not_started',
                'in_progress',
                'submitted',
                'timed_out',
                'evaluated',
                'invalidated'
            ])->default('not_started');

            $table->text('admin_feedback')->nullable();          // feedback from evaluator
            $table->foreignId('evaluated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();

            // Attempt tracking
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->boolean('is_practice')->default(false);      // practice vs real assessment

            $table->timestamps();
            $table->index(['user_id', 'question_set_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
