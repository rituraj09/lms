// database/migrations/2024_01_01_000004_create_organisations_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique()->nullable();       // Institute Code

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Media
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();

            // Info
            $table->text('description')->nullable();

            // Address
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
            $table->string('country')->default('India')->nullable();
            $table->string('postal_code')->nullable();

            // Contact Person
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();

            // Organisation Type
            $table->foreignId('organisation_type_id')
                  ->nullable()
                  ->constrained('organisation_types')
                  ->nullOnDelete();

            // Status & Limits
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->unsignedInteger('max_students')->default(0);   // 0 = unlimited

            // Subscription
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();

            // Org-level custom settings
            $table->json('settings')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
