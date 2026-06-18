<?php
// database/migrations/2024_01_01_000001_create_question_groups_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_code')->unique();
            $table->enum('questions_category', ['single', 'multiple']);
            $table->text('title')->nullable();
            $table->json('group_content')->nullable();
            $table->string('admin_note')->nullable();
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
        Schema::dropIfExists('question_groups');
    }
};
