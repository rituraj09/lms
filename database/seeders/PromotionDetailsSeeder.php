<?php

namespace Database\Seeders;

use App\Models\PromotionMaster\Promotion;
use App\Models\PromotionMaster\PromotionDetail;
use Illuminate\Database\Seeder;

class PromotionDetailsSeeder extends Seeder
{
    private const AGE_GROUP_ORDER = ['10+', '12+', '14+'];
    private const MAX_LEVEL = 7;

    private const LOW_MIN  = 50.00;
    private const LOW_MAX  = 75.00;
    private const HIGH_MIN = 75.01;
    private const HIGH_MAX = 100.00;

    public function run(): void
    {
        // Build lookup: [ageGroupName][level] => promotion_id
        $lookup = $this->buildPromotionLookup();

        $rows = [];

        foreach (self::AGE_GROUP_ORDER as $index => $ageGroupName) {
            for ($level = 1; $level <= self::MAX_LEVEL; $level++) {

                $currentId = $lookup[$ageGroupName][$level] ?? null;

                if (!$currentId) {
                    throw new \RuntimeException("Promotion not found for [{$ageGroupName} Level {$level}]. Run PromotionsSeeder first.");
                }

                if ($level < self::MAX_LEVEL) {
                    // ---- WITHIN SAME AGE GROUP: Skip Logic ----

                    $lowNextLevel  = $level + 1;
                    $highNextLevel = min($level + 2, self::MAX_LEVEL); // Cap at Level 7

                    $rows[] = $this->buildRow(
                        $currentId,
                        self::LOW_MIN,
                        self::LOW_MAX,
                        $lookup[$ageGroupName][$lowNextLevel]
                    );

                    $rows[] = $this->buildRow(
                        $currentId,
                        self::HIGH_MIN,
                        self::HIGH_MAX,
                        $lookup[$ageGroupName][$highNextLevel]
                    );

                } else {
                    // ---- LEVEL 7: Age Group Transition (No Skip) ----

                    $nextAgeGroupName = self::AGE_GROUP_ORDER[$index + 1] ?? null;

                    if ($nextAgeGroupName) {
                        // Transition to next age group's Level 1 (same target for both bands)
                        $nextId = $lookup[$nextAgeGroupName][1];

                        $rows[] = $this->buildRow($currentId, self::LOW_MIN, self::LOW_MAX, $nextId);
                        $rows[] = $this->buildRow($currentId, self::HIGH_MIN, self::HIGH_MAX, $nextId);

                    } else {
                        // ---- ABSOLUTE TERMINAL: 14+ Level 7 ----
                        $rows[] = $this->buildRow($currentId, self::LOW_MIN, self::LOW_MAX, null);
                        $rows[] = $this->buildRow($currentId, self::HIGH_MIN, self::HIGH_MAX, null);
                    }
                }
            }
        }

        // Clear existing rules before re-seeding (optional - remove if you want upsert instead)


        PromotionDetail::insert($rows);

        $this->command->info('Promotion details seeded successfully: ' . count($rows) . ' records.');
    }

    /**
     * Build lookup array: [ageGroupName][level] => promotion_id
     */
    private function buildPromotionLookup(): array
    {
        $promotions = Promotion::with(['ageGroup', 'difficultyLevel'])->get();

        $lookup = [];
        foreach ($promotions as $promotion) {
            $ageGroupName = $promotion->ageGroup->name;
            $level        = $promotion->difficultyLevel->level;

            $lookup[$ageGroupName][$level] = $promotion->id;
        }

        return $lookup;
    }

    private function buildRow(int $currentId, float $min, float $max, ?int $nextId): array
    {
        return [
            'current_promotion_id' => $currentId,
            'percentage_min'       => $min,
            'percentage_max'       => $max,
            'next_promotion_id'    => $nextId,
            'created_at'           => now(),
            'updated_at'           => now(),
        ];
    }
}
