<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_organisation', function (Blueprint $table) {
            // Existing: access_level

            // New: Store organisation-specific permissions as JSON
            $table->json('org_permissions')->nullable()->after('access_level');

            // Optional: Track who assigned and when
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->nullable();

            $table->foreign('assigned_by')
                  ->references('id')
                  ->on('admins')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_organisation', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropColumn(['org_permissions', 'assigned_by', 'assigned_at']);
        });
    }
};
