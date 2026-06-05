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
        Schema::create('question_question_set_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_set_group_id')
                ->constrained('question_set_groups')
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('order')->default(0);
            $table->decimal('score_override', 6, 2)->nullable();
            $table->integer('timer')->nullable();
            $table->decimal('negative_mark', 6, 2)->default(0);

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['question_set_group_id', 'question_id'],
                'uq_group_question'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_question_set_groups');
    }
};
