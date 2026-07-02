<?php
// app/Services/PromotionService
namespace App\Services;

use App\Models\PromotionMaster\Promotion;
use App\Models\PromotionMaster\PromotionDetail;
use App\Models\TestAttempt\UserPromotionDetail;
use App\Models\AssessmentMaster\Assessment; // Adjust namespace as per your actual model
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PromotionService
{
    /**
     * Starting point for every new user.
     */
    private const STARTING_AGE_GROUP = '10+';
    private const STARTING_DIFFICULTY_LEVEL = 1;

    /**
     * Initialize promotion records for a newly created user.
     * Creates one row per assessment type (iq, eq, lq) at the starting promotion.
     *
     * @return bool true on success, false on failure
     */
    public function initializeUserPromotion(int $userId): bool
    {
        try {
            DB::transaction(function () use ($userId) {

                $startingPromotion = Promotion::whereHas('ageGroup', function ($q) {
                    $q->where('name', self::STARTING_AGE_GROUP);
                })
                    ->whereHas('difficultyLevel', function ($q) {
                        $q->where('level', self::STARTING_DIFFICULTY_LEVEL);
                    })
                    ->first();

                if (!$startingPromotion) {
                    throw new \RuntimeException('Starting promotion configuration (10+, Level 1) not found.');
                }

                $defaultDetailId = $this->getDefaultPromotionDetailId($startingPromotion->id);

                if (!$defaultDetailId) {
                    throw new \RuntimeException('Promotion detail rule for starting promotion not found.');
                }

                foreach (\App\Helper\Globals::ASSESSMENT_TYPES as $type) {
                    UserPromotionDetail::create([
                        'user_id'              => $userId,
                        'promotion_details_id' => $defaultDetailId,
                        'assessment_type'      => $type,
                        'test_attempt_id'      => null,
                        'current_status'       => true,
                    ]);

                }
            });

            return true;

        } catch (Throwable $e) {
            Log::error('PromotionService::initializeUserPromotion failed', [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Get the user's current active promotion record for a given assessment type.
     */
    public function getCurrentUserPromotionDetail(int $userId, string $assessmentType): ?UserPromotionDetail
    {
        return UserPromotionDetail::with([
            'promotionDetail.currentPromotion.ageGroup',
            'promotionDetail.currentPromotion.difficultyLevel',
        ])
            ->where('user_id', $userId)
            ->where('assessment_type', $assessmentType)
            ->where('current_status', true)
            ->latest('id')
            ->first();
    }

    /**
     * Get the user's current Promotion (age_group + difficulty_level) for an assessment type.
     */
    public function getCurrentPromotion(int $userId, string $assessmentType): ?Promotion
    {
        $userPromotionDetail = $this->getCurrentUserPromotionDetail($userId, $assessmentType);

        return $userPromotionDetail?->promotionDetail?->currentPromotion;
    }

    /**
     * Get current promotions for ALL assessment types for a user.
     * Returns: ['iq' => Promotion, 'eq' => Promotion, 'lq' => Promotion]
     */
    public function getAllCurrentPromotions(int $userId): array
    {
        $result = [];
        foreach (\App\Helper\Globals::ASSESSMENT_TYPES as $type) {
            $result[$type] = $this->getCurrentPromotion($userId, $type);
        }
        return $result;
    }

    /**
     * Core Promotion Logic.
     * Call this after test evaluation with the calculated percentage.
     *
     * @return array{
     *     promoted: bool,
     *     message: string,
     *     current_promotion: ?Promotion,
     *     new_promotion?: ?Promotion,
     *     user_promotion_detail?: ?UserPromotionDetail
     * }
     */
    public function checkAndPromote(
        int $userId,
        string $assessmentType,
        float $percentage,
        int $testAttemptId
    ): array {
        return DB::transaction(function () use ($userId, $assessmentType, $percentage, $testAttemptId) {

            $currentUserPromotionDetail = $this->getCurrentUserPromotionDetail($userId, $assessmentType);

            if (!$currentUserPromotionDetail) {
                throw new \RuntimeException(
                    "No active promotion record found for user [{$userId}] / assessment [{$assessmentType}]"
                );
            }

            $currentPromotionId = $currentUserPromotionDetail->promotionDetail->current_promotion_id;

            // Find the rule matching the achieved percentage for the CURRENT promotion level
            $matchedRule = PromotionDetail::where('current_promotion_id', $currentPromotionId)
                ->where('percentage_min', '<=', $percentage)
                ->where('percentage_max', '>=', $percentage)
                ->first();

            // FAILED: percentage doesn't meet any passing range -> No record saved
            if (!$matchedRule) {
                return [
                    'promoted'          => false,
                    'message'           => 'Percentage does not meet the passing criteria.',
                    'current_promotion' => $currentUserPromotionDetail->promotionDetail->currentPromotion,
                ];
            }

            $nextPromotionId = $matchedRule->next_promotion_id;

            // Terminal Level (no next promotion defined e.g. 14+ Level 7 max)
            if (!$nextPromotionId) {
                // Still record the passing test_attempt_id against the existing (current) record
                $currentUserPromotionDetail->update([
                    'test_attempt_id' => $testAttemptId,
                ]);

                return [
                    'promoted'              => false,
                    'message'               => 'User has already reached the maximum promotion level. Attempt recorded.',
                    'current_promotion'     => $currentUserPromotionDetail->promotionDetail->currentPromotion,
                    'user_promotion_detail' => $currentUserPromotionDetail,
                ];
            }

            $nextDefaultDetailId = $this->getDefaultPromotionDetailId($nextPromotionId);

            if (!$nextDefaultDetailId) {
                throw new \RuntimeException(
                    "No promotion_details rule configured for promotion ID [{$nextPromotionId}]"
                );
            }

            // Deactivate current promotion record
            $currentUserPromotionDetail->update(['current_status' => false]);

            // Insert new active promotion record with the passing test_attempt_id
            $newRecord = UserPromotionDetail::create([
                'user_id'              => $userId,
                'promotion_details_id' => $nextDefaultDetailId,
                'assessment_type'      => $assessmentType,
                'test_attempt_id'      => $testAttemptId,
                'current_status'       => true,
            ]);

            return [
                'promoted'              => true,
                'message'               => 'User promoted successfully.',
                'previous_promotion'    => $currentUserPromotionDetail->promotionDetail->currentPromotion,
                'new_promotion'         => Promotion::find($nextPromotionId),
                'user_promotion_detail' => $newRecord,
            ];
        });
    }


    /**
     * Get promotion outcome info for a specific test attempt.
     * Used to display promotion result on the result page.
     */
    public function getPromotionInfoForAttempt(int $testAttemptId): array
    {
        $record = UserPromotionDetail::where('test_attempt_id', $testAttemptId)
            ->with([
                'promotionDetail.currentPromotion.ageGroup',
                'promotionDetail.currentPromotion.difficultyLevel',
            ])
            ->first();

        // No record found = attempt did not meet passing criteria
        if (!$record) {
            return [
                'promoted'        => false,
                'is_terminal'     => false,
                'assessment_type' => null,
                'promotion'       => null,
            ];
        }

        $promotion  = $record->promotionDetail->currentPromotion;
        $isTerminal = $this->isTerminalPromotion($promotion->id);

        return [
            'promoted'        => true,
            'is_terminal'     => $isTerminal,
            'assessment_type' => $record->assessment_type,
            'promotion'       => [
                'id'               => $promotion->id,
                'name'             => $promotion->name,
                'age_group'        => $promotion->ageGroup->name,
                'difficulty_level' => $promotion->difficultyLevel->level,
                'badge'            => $promotion->badge,
            ],
        ];
    }

    /**
     * Check if a promotion is the maximum level (no further promotion possible).
     */
    public function isTerminalPromotion(int $promotionId): bool
    {
        return !PromotionDetail::where('current_promotion_id', $promotionId)
            ->whereNotNull('next_promotion_id')
            ->exists();
    }
    /**
     * Get the default promotion_details row ID for a given promotion (current_promotion_id).
     * Since a promotion can have multiple percentage-range rules,
     * we pick the one with the lowest percentage_min as the "default" reference row.
     */
    public function getDefaultPromotionDetailId(int $promotionId): ?int
    {
        return PromotionDetail::where('current_promotion_id', $promotionId)
            ->orderBy('percentage_min')
            ->value('id');
    }

    /**
     * Get assessments matching the user's CURRENT promotion level for a given assessment type.
     * (i.e., assessments the user should attempt next)
     *
     * NOTE: assessments.assessment_type_id is an ENUM column (same values as ASSESSMENT_TYPES),
     * not an actual foreign key despite the "_id" suffix.
     */
    public function getNextAssessments(int $userId, string $assessmentType): Collection
    {
        $currentPromotion = $this->getCurrentPromotion($userId, $assessmentType);

        if (!$currentPromotion) {
            return collect();
        }

        return Assessment::where('age_group_id', $currentPromotion->age_group_id)
            ->where('difficulty_level_id', $currentPromotion->difficulty_level_id)
            ->where('assessment_type_id', $assessmentType) // enum column
            ->get();
    }

    /**
     * Get assessments for ALL assessment types matching user's current promotions.
     * Returns: ['iq' => Collection, 'eq' => Collection, 'lq' => Collection]
     */
    public function getAllNextAssessments(int $userId): array
    {
        $result = [];
        foreach (ASSESSMENT_TYPES as $type) {
            $result[$type] = $this->getNextAssessments($userId, $type);
        }
        return $result;
    }
}
