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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('lg_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
        });
        DB::table('states')->insert([
            ['id'=>'1','lg_code'=>'35','name'=>'Andaman And Nicobar Islands'],
            ['id'=>'2','lg_code'=>'28','name'=>'Andhra Pradesh'],
            ['id'=>'3','lg_code'=>'12','name'=>'Arunachal Pradesh'],
            ['id'=>'4','lg_code'=>'18','name'=>'Assam'],
            ['id'=>'5','lg_code'=>'10','name'=>'Bihar'],
            ['id'=>'6','lg_code'=>'4','name'=>'Chandigarh'],
            ['id'=>'7','lg_code'=>'22','name'=>'Chhattisgarh'],
            ['id'=>'8','lg_code'=>'7','name'=>'Delhi'],
            ['id'=>'9','lg_code'=>'30','name'=>'Goa'],
            ['id'=>'10','lg_code'=>'24','name'=>'Gujarat'],
            ['id'=>'11','lg_code'=>'6','name'=>'Haryana'],
            ['id'=>'12','lg_code'=>'2','name'=>'Himachal Pradesh'],
            ['id'=>'13','lg_code'=>'1','name'=>'Jammu And Kashmir'],
            ['id'=>'14','lg_code'=>'20','name'=>'Jharkhand'],
            ['id'=>'15','lg_code'=>'29','name'=>'Karnataka'],
            ['id'=>'16','lg_code'=>'32','name'=>'Kerala'],
            ['id'=>'17','lg_code'=>'37','name'=>'Ladakh'],
            ['id'=>'18','lg_code'=>'31','name'=>'Lakshadweep'],
            ['id'=>'19','lg_code'=>'23','name'=>'Madhya Pradesh'],
            ['id'=>'20','lg_code'=>'27','name'=>'Maharashtra'],
            ['id'=>'21','lg_code'=>'14','name'=>'Manipur'],
            ['id'=>'22','lg_code'=>'17','name'=>'Meghalaya'],
            ['id'=>'23','lg_code'=>'15','name'=>'Mizoram'],
            ['id'=>'24','lg_code'=>'13','name'=>'Nagaland'],
            ['id'=>'25','lg_code'=>'21','name'=>'Odisha'],
            ['id'=>'26','lg_code'=>'34','name'=>'Puducherry'],
            ['id'=>'27','lg_code'=>'3','name'=>'Punjab'],
            ['id'=>'28','lg_code'=>'8','name'=>'Rajasthan'],
            ['id'=>'29','lg_code'=>'11','name'=>'Sikkim'],
            ['id'=>'30','lg_code'=>'33','name'=>'Tamil Nadu'],
            ['id'=>'31','lg_code'=>'36','name'=>'Telangana'],
            ['id'=>'32','lg_code'=>'38','name'=>'The Dadra And Nagar Haveli And Daman And Diu'],
            ['id'=>'33','lg_code'=>'16','name'=>'Tripura'],
            ['id'=>'34','lg_code'=>'5','name'=>'Uttarakhand'],
            ['id'=>'35','lg_code'=>'9','name'=>'Uttar Pradesh'],
            ['id'=>'36','lg_code'=>'19','name'=>'West Bengal']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
