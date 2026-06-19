// database/migrations/xxxx_create_student_transfers_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_organisation_id')->nullable()->constrained('organisations')->nullOnDelete();
            $table->foreignId('to_organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('transferred_by')->constrained('admins')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('transferred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_transfers');
    }
};
