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
            $table->unsignedBigInteger('current_age_group_id')->nullable()->after('extra_data');
            $table->foreign('current_age_group_id')->references('id')->on('age_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropForeign(['current_age_group_id']);
            $table->dropColumn('current_age_group_id');
        });
    }
};
