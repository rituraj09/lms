<?php
// database/migrations/xxxx_xx_xx_create_assessment_organisation_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_organisation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->enum('status', ['active', 'inactive', 'expired'])
                ->default('active');

            $table->date('assigned_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->text('assignment_note')->nullable();

            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['assessment_id', 'organisation_id']);

            // Indexes for performance
            $table->index('status');
            $table->index('assigned_date');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_organisation');
    }
};
