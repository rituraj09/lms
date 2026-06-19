// database/migrations/2024_01_01_000003_create_organisation_types_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // e.g. Institute, Company, NGO
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();         // e.g. fas fa-university
            $table->string('color')->default('#6c757d');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_types');
    }
};
