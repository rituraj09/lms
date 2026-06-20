// database/migrations/xxxx_create_admin_organisation_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_organisation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->enum('access_level', ['full', 'limited', 'view_only'])->default('limited');
            $table->timestamps();

            $table->unique(['admin_id', 'organisation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_organisation');
    }
};
