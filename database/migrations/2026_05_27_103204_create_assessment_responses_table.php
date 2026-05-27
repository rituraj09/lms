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
        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->foreignId('question_type_id')->constrained()->restrictOnDelete(); // denormalized for fast queries

            // Raw response stored as JSON — structure mirrors question_contents
            /*
            * MCQ:            {"selected": "A"}
            * Multi-Select:   {"selected": ["A", "C"]}
            * Numerical:      {"value": 42.5}
            * Sequencing:     {"order": [3, 1, 4, 2]}
            * Match:          {"pairs": [{"left":"L1","right":"R2"}, ...]}
            * Likert:         {"rating": 4}
            * Open Text:      {"text": "Student's open response..."}
            * Ranking:        {"ranks": [2, 4, 1, 3]}
            * Yes/No:         {"selected": "yes"}
            * Case Study:     {"sub_answers": [{"stem_index":0,"selected":"B"}, ...]}
            * Rapid Fire:     {"answer": "Apple"}
            * Situation:      {"selected": "B"}
            */
            $table->json('response')->nullable();                // null = skipped

            // Scoring
            $table->decimal('score_earned', 6, 2)->default(0);
            $table->decimal('max_score', 6, 2)->default(1);
            $table->boolean('is_correct')->nullable();           // null for subjective/partial types
            $table->decimal('accuracy', 5, 2)->nullable();       // 0–100 for partial credit types

            // Timing
            $table->unsignedSmallInteger('time_taken')->nullable(); // seconds on this question
            $table->boolean('is_skipped')->default(false);
            $table->boolean('is_flagged')->default(false);       // student flagged for review

            // For manual/open-text scoring by admin
            $table->boolean('requires_manual_grading')->default(false);
            $table->text('admin_score_notes')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();

            $table->timestamps();
            $table->index(['assessment_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_responses');
    }
};
