<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('age_group_id')
                ->constrained('age_groups')
                ->cascadeOnDelete();
            $table->foreignId('difficulty_level_id')
                ->constrained('difficulty_levels')
                ->cascadeOnDelete();
            $table->string('badge')->nullable(); // image path
            $table->unique(['age_group_id', 'difficulty_level_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
