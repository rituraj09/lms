<?php

namespace Database\Seeders;

use App\Models\PromotionMaster\Promotion;
use App\Models\EvaluationMaster\AgeGroup;
use App\Models\EvaluationMaster\DifficultyLevel;
use Illuminate\Database\Seeder;

class PromotionsSeeder extends Seeder
{
    /**
     * Age groups in progression order.
     */
    private const AGE_GROUP_ORDER = ['10+', '12+', '14+'];

    public function run(): void
    {
        $ageGroups = AgeGroup::whereIn('name', self::AGE_GROUP_ORDER)
            ->pluck('id', 'name');

        $difficultyLevels = DifficultyLevel::whereBetween('level', [1, 7])
            ->pluck('id', 'level');

        // Validation
        foreach (self::AGE_GROUP_ORDER as $ageGroupName) {
            if (!isset($ageGroups[$ageGroupName])) {
                throw new \RuntimeException("Age group [{$ageGroupName}] not found. Please seed age_groups table first.");
            }
        }

        for ($level = 1; $level <= 7; $level++) {
            if (!isset($difficultyLevels[$level])) {
                throw new \RuntimeException("Difficulty level [{$level}] not found. Please seed difficulty_levels table first.");
            }
        }

        $promotionsData = [];

        foreach (self::AGE_GROUP_ORDER as $ageGroupName) {
            for ($level = 1; $level <= 7; $level++) {
                $promotionsData[] = [
                    'name'                => "{$ageGroupName} Level {$level}",
                    'age_group_id'        => $ageGroups[$ageGroupName],
                    'difficulty_level_id' => $difficultyLevels[$level],
                    'badge'               => null, // Update later with actual image paths
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }
        }

        // Use upsert to avoid duplicate entry errors on re-seed
        Promotion::upsert(
            $promotionsData,
            ['age_group_id', 'difficulty_level_id'], // unique constraint columns
            ['name', 'badge', 'updated_at']
        );

        $this->command->info('Promotions seeded successfully: ' . count($promotionsData) . ' records.');
    }
}
