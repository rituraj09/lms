<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Master\AdminDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run(): void
    {
        // ── Super Admin ───────────────────────────────────────────
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@lms.com'],
            [
                'name'     => 'Super Admin',
                'mobile'   => '7002274743',
                'password' => Hash::make('123456'),
                'status'   => 'active',
            ]
        );
        $superAdmin->assignRole('super_admin');

        AdminDetail::firstOrCreate(
            ['admin_id' => $superAdmin->id],
            [
                'first_name'  => 'Super',
                'last_name'   => 'Admin',
                'designation' => 'System Administrator',
            ]
        );

        // ── Admin ─────────────────────────────────────────────────
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name'     => 'Admin User',
                'mobile'   => '9000000002',
                'password' => Hash::make('Admin@1234'),
                'status'   => 'active',
            ]
        );
        $admin->assignRole('admin');

        AdminDetail::firstOrCreate(
            ['admin_id' => $admin->id],
            [
                'first_name'  => 'Admin',
                'last_name'   => 'User',
                'designation' => 'Administrator',
            ]
        );

        // ── Master Trainer ────────────────────────────────────────
        $trainer = Admin::firstOrCreate(
            ['email' => 'trainer@lms.com'],
            [
                'name'     => 'Master Trainer',
                'mobile'   => '9000000003',
                'password' => Hash::make('Admin@1234'),
                'status'   => 'active',
            ]
        );
        $trainer->assignRole('master_trainer');

        AdminDetail::firstOrCreate(
            ['admin_id' => $trainer->id],
            [
                'first_name'  => 'Master',
                'last_name'   => 'Trainer',
                'designation' => 'Master Trainer',
            ]
        );
    }
}
