// database/migrations/2024_01_01_000002_create_admin_details_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                  ->constrained('admins')
                  ->cascadeOnDelete();

            // Personal Info
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();

            // Contact & Address
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('state_id')
                  ->nullable()
                  ->constrained('states')
                  ->nullOnDelete();
            $table->foreignId('district_id')
                  ->nullable()
                  ->constrained('districts')
                  ->nullOnDelete();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            // Professional
            $table->string('designation')->nullable();   // e.g. "Senior Trainer"
            $table->string('qualification')->nullable();  // e.g. "B.Ed"
            $table->string('expertise')->nullable();      // e.g. "Mathematics"
            $table->text('bio')->nullable();

            // Social
            $table->json('social_links')->nullable();     // { linkedin, twitter, etc }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_details');
    }
};
