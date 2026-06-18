<?php
// database/migrations/2024_01_01_000002_create_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_group_id')
                  ->constrained('question_groups')
                  ->cascadeOnDelete();
            $table->string('question_code')->unique();
            $table->foreignId('primary_skill_id')
                  ->constrained('primary_skill_types')
                  ->restrictOnDelete();
            $table->foreignId('sub_skill_id')
                  ->constrained('sub_skill_types')
                  ->restrictOnDelete();
            $table->foreignId('difficulty_level_id')
                  ->constrained('difficulty_levels')
                  ->restrictOnDelete();
            $table->foreignId('age_group_id')
                  ->constrained('age_groups')
                  ->restrictOnDelete();
            $table->enum('answer_category', ['single_optional', 'multi_optional', 'open_text']);
            $table->json('question_content')->nullable();
            $table->text('explaination')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
