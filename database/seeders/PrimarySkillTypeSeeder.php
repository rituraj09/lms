<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EvaluationMaster\PrimarySkillType;

class PrimarySkillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'name' => 'Cognitive Skill',
                'slug' => 'cognitive',
                'description' => 'Skills related to mental processes.',
            ],

            [
                'name' => 'Life Skill',
                'slug' => 'life',
                'description' => 'Interpersonal and intrapersonal skills.',
            ],

            [
                'name' => 'Leadership Skill',
                'slug' => 'leadership',
                'description' => 'Skills related to leadership and influence.',
            ],
        ];

        foreach ($skills as $skill) {
            PrimarySkillType::updateOrCreate(
                ['slug' => $skill['slug']],
                $skill
            );
        }
    }
}
