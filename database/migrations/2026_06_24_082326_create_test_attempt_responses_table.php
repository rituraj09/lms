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
        Schema::create('test_attempt_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('test_attempt_id')
                ->constrained('test_attempts')
                ->cascadeOnDelete();

            $table->foreignId('assessment_question_id')
                ->constrained('assessment_questions')
                ->cascadeOnDelete();

            $table->json('response')->nullable();

            $table->decimal('obtained_marks', 8, 2)
                ->default(0);

            $table->boolean('is_correct')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'test_attempt_id',
                'assessment_question_id'
            ], 'attempt_question_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_attempt_responses');
    }
};
