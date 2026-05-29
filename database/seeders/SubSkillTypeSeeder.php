<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EvaluationMaster\SubSkillType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class SubSkillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
        $subSkills = [
            'Attention',
            'Memory',
            'Logical Thinking',
            'Problem Solving',
            'Decision Making',
            'Critical Thinking',
            'Abstract Thinking',
            'Creative Thinking',
            'Metacognition',
            'Hypothetical Thinking',
            'Self-Awareness',
            'Empathy',
            'Communication',
            'Interpersonal Relationship',
            'Coping With Stress',
            'Managing Emotions',
            'Collaboration & Team Work',
            'Time Management',
            'Organizational Skill',
            'Entrepreneurship Skill',
            'Financial Skill',
            'Adaptability & Flexibility',
            'Resilience',
            'Persuasion and Negotiation',
            'Delegation',
            'Self-Regulation',
        ];

        foreach ($subSkills as $name) {
            DB::table('sub_skill_types')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'name'       => $name,
                    'slug'       => Str::slug($name),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
    }
}
