<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\EvaluationMaster\DifficultyLevel;
class DifficultyLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $levels = [
            ['level' => 1, 'name' => 'Level 1 – Foundation', 'color_code' => '#4CAF50'],
            ['level' => 2, 'name' => 'Level 2 – Elementary', 'color_code' => '#8BC34A'],
            ['level' => 3, 'name' => 'Level 3 – Intermediate', 'color_code' => '#FFC107'],
            ['level' => 4, 'name' => 'Level 4 – Upper Mid', 'color_code' => '#FF9800'],
            ['level' => 5, 'name' => 'Level 5 – Advanced', 'color_code' => '#FF5722'],
            ['level' => 6, 'name' => 'Level 6 – Expert', 'color_code' => '#E91E63'],
            ['level' => 7, 'name' => 'Level 7 – Master', 'color_code' => '#9C27B0'],
        ];

        foreach ($levels as $level) {
            DifficultyLevel::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
