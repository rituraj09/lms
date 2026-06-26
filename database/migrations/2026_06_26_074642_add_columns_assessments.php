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
        Schema::table('assessments', function (Blueprint $table) {
            $table->integer('max_attempts')->default(1)->after('status');;
            $table->boolean('shuffle_sections')->default(false)->after('max_attempts');
            $table->boolean('show_result_immediately')->default(true)->after('shuffle_sections'); //** Show result after submission */
            $table->boolean('show_correct_answers')->default(false)->after('show_result_immediately'); //** Show correct answer with result */
            $table->boolean('show_explainations')->default(false)->after('show_correct_answers'); //** Show correct answer with correct answer */

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
