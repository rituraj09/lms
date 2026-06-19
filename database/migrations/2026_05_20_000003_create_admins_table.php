// database/migrations/2024_01_01_000001_create_admins_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // Current Context (session-like persistence)
            // current_role_id     → removed (handled by Spatie roles)
            // current_organisation_id → kept but as foreignId properly


            $table->foreignId('current_organisation_id')
                  ->nullable()
                  ->constrained('organisations')
                  ->nullOnDelete();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
