// database/migrations/2024_01_01_000012_add_custom_columns_to_permission_tables.php
// Run AFTER: php artisan vendor:publish --provider="Spatie\Permission\PermissionRegistrar"

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
            $table->text('description')->nullable()->after('display_name');
            $table->boolean('is_system')->default(false)->after('description');
            $table->string('color')->default('#6c757d')->after('is_system');
            $table->string('icon')->default('fas fa-user')->after('color');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
            $table->string('group')->nullable()->after('display_name');
            $table->text('description')->nullable()->after('group');
            $table->unsignedInteger('sort_order')->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'description', 'is_system', 'color', 'icon']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'group', 'description', 'sort_order']);
        });
    }
};
