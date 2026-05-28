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
             [
                'name' => 'Sequencing',
                'slug' => 'sequencing',
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Student arranges items in a specific order.',
            ],

             [
                'name' => 'Match the Following',
                'slug' => 'matching',
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Student matches items from two lists.',
            ],

             [
                'name' => 'Pattern Series Completion',
                'slug' => 'pattern_series',
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Student identifies the pattern in a series of items.',
            ],

             [
                'name' => 'Image based reasoning',
                'slug' => 'image_reasoning',
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Student analyzes an image and provides a response.',
            ],

             [
                'name' => 'Memory Recall',
                'slug' => 'memory_recall',
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => 'Student recalls and provides information from memory.',
            ],

             [
                'name' => 'Situation Judgement',
                'slug' => 'situation_judgement',
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student evaluates a scenario and provides a judgment.",
            ],

             [
                'name' => "Likert Scale",
                'slug' => "likert_scale",
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student indicates their level of agreement or disagreement with a statement.",
            ],

             [
                'name' => "Open Text Response",
                'slug' => "open_text_response",
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student writes a detailed text response.",
            ],
            [
                'name' => "Ranking Preferences",
                'slug' => "ranking_preferences",
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student ranks a set of options based on their preferences.",
            ],
             [
                'name' => "Yes/No Behavioural",
                'slug' => "yes_no_behavioural",
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student provides a yes or no response to a behavioural question.",
            ],
                [
                'name' => "Case Study Analysis",
                'slug' => "case_study_analysis",
                'accuracy_factor' => true,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student analyzes a case study and provides a response.",
            ],
               [
                'name' => "Rapid Fire",
                'slug' => "rapid_fire",
                'accuracy_factor' => false,
                'time_factor' => true,
                'scoring_weightage' => null,
                'description' => "Student answers a series of quick questions.",
            ],

        ];

        foreach ($types as $type) {
            QuestionType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }

}
