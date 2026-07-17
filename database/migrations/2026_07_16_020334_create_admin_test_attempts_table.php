<?php
// database/migrations/xxxx_create_admin_test_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin test attempts (separate from user test_attempts)
        Schema::create('admin_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('ack_no')->unique();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('negative_score', 8, 2)->default(0);
            $table->enum('status', ['in_progress', 'submitted', 'evaluated'])->default('in_progress');
            $table->timestamps();
        });

        // Admin test attempt responses
        Schema::create('admin_test_attempt_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_test_attempt_id')
                ->constrained('admin_test_attempts')
                ->onDelete('cascade');
            $table->foreignId('assessment_question_id')
                ->constrained('assessment_questions')
                ->onDelete('cascade');
            $table->json('response')->nullable();
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->boolean('is_correct')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_test_attempt_responses');
        Schema::dropIfExists('admin_test_attempts');
    }
};
