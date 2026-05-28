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
        Schema::create('admin_details', function (Blueprint $table) {
                $table->id();
               $table->foreignId('admin_id')->constrained('admins');
               $table->foreignId('designation_id')->nullable()->constrained('designations');
               $table->enum('gender', ['Male', 'Female','Other'])->default('Male')->nullable();
               $table->date('dob')->nullable();
               $table->text('address')->nullable();
               $table->string('pincode')->nullable();
               $table->foreignId('state_id')->nullable()->constrained('states');
               $table->foreignId('district_id')->nullable()->constrained('districts');
               $table->string('pdf_path')->nullable();
               $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_details');
    }
};
