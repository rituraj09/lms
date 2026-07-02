<?php
// database/migrations/xxxx_xx_xx_alter_promotion_details_nullable_next.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_details', function (Blueprint $table) {
            $table->dropForeign(['next_promotion_id']);
            $table->foreignId('next_promotion_id')
                ->nullable()
                ->change();
            $table->foreign('next_promotion_id')
                ->references('id')->on('promotions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('promotion_details', function (Blueprint $table) {
            $table->dropForeign(['next_promotion_id']);
            $table->foreignId('next_promotion_id')
                ->nullable(false)
                ->change();
            $table->foreign('next_promotion_id')
                ->references('id')->on('promotions')
                ->cascadeOnDelete();
        });
    }
};
