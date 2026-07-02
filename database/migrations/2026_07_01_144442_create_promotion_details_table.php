<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('current_promotion_id')
                ->constrained('promotions')
                ->cascadeOnDelete();
            $table->decimal('percentage_min', 5, 2);
            $table->decimal('percentage_max', 5, 2);
            $table->foreignId('next_promotion_id')
                ->constrained('promotions')
                ->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_details');
    }
};
