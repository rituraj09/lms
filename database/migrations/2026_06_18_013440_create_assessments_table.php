<?php
// database/migrations/2024_01_01_000003_create_assessments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('assessment_code')->unique();
            $table->text('title');
            $table->text('instructions')->nullable();
            $table->enum('assessment_type_id', [
                'iq', 'eq', 'lq',
                'iq&eq', 'iq&lq', 'eq&lq',
                'iq&eq&lq'
            ]);
            $table->foreignId('age_group_id')
                  ->constrained('age_groups')
                  ->restrictOnDelete();
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('passing_marks', 8, 2)->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->text('admin_note')->nullable();
            $table->boolean('has_negative_mark')->default(false);
            $table->enum('status', ['draft', 'publish', 'unpublish'])
                  ->default('draft');
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
        Schema::dropIfExists('assessments');
    }
};
