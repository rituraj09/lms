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
        Schema::table('user_details', function (Blueprint $table) {
            // Drop the old state column
            $table->dropColumn('state');

            // Add new foreign keys
            $table->foreignId('state_id')->nullable()->after('city')->constrained('states')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('state_id')->constrained('districts')->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
