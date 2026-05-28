<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AgeGroup;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $groups = [
            ['name' => '10+', 'min_age' => 10, 'max_age' => null],
            ['name' => '12+', 'min_age' => 12, 'max_age' => null],
            ['name' => '14+', 'min_age' => 14, 'max_age' => null],
        ];

        foreach ($groups as $group) {
            AgeGroup::updateOrCreate(
                ['name' => $group['name']],
                $group
            );
        }
    }
}
