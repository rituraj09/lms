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
        Schema::create('question_set_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_set_id')->constrained();
            $table->text('title');
            $table->json('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('question_category', ['optional', 'follow-up question', 'open-text'])->default('optional');
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('allow_main_backtrack')->default(true); // false for memory recall
            $table->boolean('allow_backtrack')->default(true); // false in rapid fire mode
            $table->boolean('main_timer')->default(false); // true for memory recall
            $table->foreignId('created_by')->constrained('admins')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_set_groups');
    }
};
