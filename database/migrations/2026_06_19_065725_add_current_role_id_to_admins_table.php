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
       Schema::table('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('current_role_id')->nullable()->after('status');
            $table->foreign('current_role_id')
                  ->references('id')
                  ->on('roles')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['current_role_id']);
            $table->dropColumn('current_role_id');
        });
    }
};
