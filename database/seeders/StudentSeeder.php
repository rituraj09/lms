<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Master\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name'            => 'Alex joye',
            'email'           => 'alex@example.com',
            'phone'           => '7002274743',
            'password'        => Hash::make('123456'),
            'status'          => 'active',
            'organisation_id' => 1, // Ensure organisation exists
        ]);

        UserDetail::updateOrCreate([
            'user_id'                   => $user->id,
            'student_id'                => 'STU' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            'first_name'                => 'Alex',
            'last_name'                 => 'joye',
            'gender'                    => 'male',
            'date_of_birth'             => '2005-06-15',
            'address_line1'             => '123 Main Road',
            'address_line2'             => 'Near City Center',
            'city'                      => 'Delhi',
            'state'                     => 'Delhi',
            'country'                   => 'India',
            'postal_code'               => '110001',
            'emergency_contact_name'    => 'Ramesh Sharma',
            'emergency_contact_phone'   => '9876543211',
            'bio'                       => 'Sample student account for testing.',
            'extra_data'                => [
                'class' => '10',
                'section' => 'A',
            ],
        ]);


    }
}
