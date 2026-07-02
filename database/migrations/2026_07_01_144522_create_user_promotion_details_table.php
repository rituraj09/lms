<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_promotion_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('promotion_details_id')
                ->constrained('promotion_details')
                ->cascadeOnDelete();
            $table->enum('assessment_type', \App\Helper\Globals::ASSESSMENT_TYPES);
            $table->foreignId('test_attempt_id')
                ->nullable()
                ->constrained('test_attempts')
                ->cascadeOnDelete();
            $table->boolean('current_status')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'user_id',
                'promotion_details_id',
                'assessment_type'
            ], 'unique_key_prom_ass');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_promotion_details');
    }
};
