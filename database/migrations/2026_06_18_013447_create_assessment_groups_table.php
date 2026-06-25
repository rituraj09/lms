<?php
// database/migrations/2024_01_01_000004_create_assessment_groups_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')
                  ->constrained('assessments')
                  ->cascadeOnDelete();
            $table->foreignId('question_group_id')
                  ->constrained('question_groups')
                  ->restrictOnDelete();
            $table->text('instructions')->nullable();
            $table->boolean('suffle_question')->default(false);
            $table->boolean('allow_back_to_group_question')->default(true);
            $table->boolean('allow_back_to_previous_question')->default(true);
            $table->integer('group_timer')->default(0);
            $table->text('admin_note')->nullable();
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
        Schema::dropIfExists('assessment_groups');
    }
};
