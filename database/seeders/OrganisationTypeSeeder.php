<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrganisationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $organisationTypes = [
            [
                'name' => 'Institute',
                'slug' => 'institute',
                'description' => 'Educational institutions, schools, colleges and universities.',
                'icon' => 'fas fa-university',
                'color' => '#0d6efd',
                'sort_order' => 1,
            ],
            [
                'name' => 'Company',
                'slug' => 'company',
                'description' => 'Private and public sector companies.',
                'icon' => 'fas fa-building',
                'color' => '#198754',
                'sort_order' => 2,
            ],
            [
                'name' => 'NGO',
                'slug' => 'ngo',
                'description' => 'Non-governmental and non-profit organizations.',
                'icon' => 'fas fa-hands-helping',
                'color' => '#fd7e14',
                'sort_order' => 3,
            ],
            [
                'name' => 'Government Department',
                'slug' => 'government-department',
                'description' => 'Government ministries, departments and agencies.',
                'icon' => 'fas fa-landmark',
                'color' => '#6f42c1',
                'sort_order' => 4,
            ],
            [
                'name' => 'Training Center',
                'slug' => 'training-center',
                'description' => 'Professional and vocational training centers.',
                'icon' => 'fas fa-chalkboard-teacher',
                'color' => '#dc3545',
                'sort_order' => 5,
            ],
            [
                'name' => 'Research Organization',
                'slug' => 'research-organization',
                'description' => 'Research institutes and think tanks.',
                'icon' => 'fas fa-flask',
                'color' => '#20c997',
                'sort_order' => 6,
            ],
        ];

        foreach ($organisationTypes as $type) {
            DB::table('organisation_types')->updateOrInsert(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'icon' => $type['icon'],
                    'color' => $type['color'],
                    'is_active' => true,
                    'sort_order' => $type['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
