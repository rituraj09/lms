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
        Schema::create('assessment_skill_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('primary_skill_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('sub_skill_type_id')->constrained()->restrictOnDelete();

            $table->unsignedTinyInteger('questions_attempted')->default(0);
            $table->unsignedTinyInteger('questions_correct')->default(0);
            $table->decimal('raw_score', 6, 2)->default(0);
            $table->decimal('max_score', 6, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('proficiency_level')->nullable(); // "Developing", "Proficient", "Advanced"

            $table->timestamps();
            $table->unique(['assessment_id', 'sub_skill_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_skill_scores');
    }
};
