<?php
// database/migrations/2024_01_01_000005_create_assessment_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_group_id')
                  ->constrained('assessment_groups')
                  ->cascadeOnDelete();
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->restrictOnDelete();
            $table->decimal('negative_mark', 8, 2)->default(0);
            $table->integer('question_timer')->default(0);

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
        Schema::dropIfExists('assessment_questions');
    }
};
