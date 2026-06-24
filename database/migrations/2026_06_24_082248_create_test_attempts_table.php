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
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();

            $table->string('ack_no')->unique();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();

            $table->decimal('negative_score', 8, 2)->default(0);
            $table->decimal('total_score', 8, 2)->default(0);

            $table->enum('status', [
                'in_progress',
                'submitted',
                'evaluated'
            ])->default('in_progress');

            $table->timestamps();

            $table->index(['assessment_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
