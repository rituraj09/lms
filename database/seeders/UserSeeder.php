<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \DB::table('admins')->insert([
            ['name'=>'Ashim Sarma','email'=>'ashimxyz@gmail.com','password'=>bcrypt('123456'),'mobile'=>'7002274743'],
        ]);

        \DB::table('roles')->insert([
            ['name'=>'Admin', 'guard_name'=>'admin'],
        ]);
        $admin = Admin::find(1);
        setPermissionsTeamId(null);
        $admin->assignRole('Admin');
        $this->call(AgeGroupSeeder::class);
        $this->call(DifficultyLevelSeeder::class);
        $this->call(PrimarySkillTypeSeeder::class);
        $this->call(QuestionTypeSeeder::class);
        $this->call(SubSkillTypeSeeder::class);

    }
}
