<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EvaluationMaster\QuestionType;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $types = [
            [
                'name' => 'Multiple Choice Question',
                'slug' => 'mcq',
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Single correct answer from multiple options.',
            ],

            [
                'name' => 'Multi Select Question',
                'slug' => 'msq',
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'One or more correct answers from multiple options.',
            ],

            [
                'name' => 'Numerical Answer',
                'slug' => 'numerical',
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Student types a numeric value.',
            ],

            // Add remaining types...
        ];

        foreach ($types as $type) {
            QuestionType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }

}
