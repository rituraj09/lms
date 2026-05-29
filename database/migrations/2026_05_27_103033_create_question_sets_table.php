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
        Schema::create('question_sets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();  // e.g. "IQ_SET_001"
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('question_set_type', ['iq', 'eq', 'lq']); // IQ, EQ, Leadership Quotient
            $table->text('description')->nullable();
            $table->foreignId('age_group_id')->constrained();
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('timer')->nullable();  // total set timer in seconds
            $table->unsignedSmallInteger('total_questions')->default(0); // denormalized count
            $table->unsignedSmallInteger('passing_score')->nullable();   // min score to pass
            $table->boolean('randomize_questions')->default(false);
            $table->enum('status', ['draft', 'publish', 'unpublish'])->default('draft');
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
        Schema::dropIfExists('question_sets');
    }
};
