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
        Schema::create('question_types', function (Blueprint $table) {
             $table->id();
            $table->string('name');                          // e.g. "Multiple Choice Question"
            $table->string('slug')->unique();                // e.g. "mcq", "msq", "numerical"
            $table->boolean('accuracy_factor')->default(false); // from Question_Type.docx
            $table->boolean('time_factor')->default(true);      // all have time factor
            $table->decimal('scoring_weightage', 5, 2)->nullable(); // Scoring Weightage column
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_types');
    }
};
