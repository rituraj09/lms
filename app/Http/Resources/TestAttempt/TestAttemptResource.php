<?php
namespace App\Http\Resources\TestAttempt;

use App\Http\Resources\Assessment\AssessmentDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Safe total questions count
        $totalQuestions = 0;
        try {
            if ($this->relationLoaded('assessment') && $this->assessment) {
                if ($this->assessment->relationLoaded('assessmentGroups')) {
                    $totalQuestions = $this->assessment->assessmentGroups
                        ->sum(fn($g) => $g->relationLoaded('assessmentQuestions')
                            ? $g->assessmentQuestions->count()
                            : 0
                        );
                }
            }
        } catch (\Throwable $e) {
            $totalQuestions = 0;
        }

        // Safe answered count
        $answeredQuestions = 0;
        try {
            if ($this->relationLoaded('responses')) {
                $answeredQuestions = $this->responses
                    ->whereNotNull('response')
                    ->count();
            }
        } catch (\Throwable $e) {
            $answeredQuestions = 0;
        }

        return [
            'id'                 => $this->id,
            'ack_no'             => $this->ack_no,
            'assessment_id'      => $this->assessment_id,
            'user_id'            => $this->user_id,
            'started_at'         => $this->started_at?->toIso8601String(),
            'submitted_at'       => $this->submitted_at?->toIso8601String(),
            'negative_score'     => (float) $this->negative_score,
            'total_score'        => (float) $this->total_score,
            'status'             => $this->status,

            // ✅ Safe assessment_type_id access
            'assessment_type_id' => $this->assessment?->assessment_type_id ?? null,

            // Computed
            'total_questions'    => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'time_elapsed'       => $this->getTimeElapsed(),
            'time_remaining'     => $this->getTimeRemaining(),
            'duration_minutes'   => $this->getDurationInMinutes(),
            'has_expired'        => $this->hasExpired(),

            // ✅ Keep original structure
            'assessment' => $this->whenLoaded(
                'assessment',
                fn() => new AssessmentDetailResource($this->assessment)
            ),

            'responses' => $this->whenLoaded(
                'responses',
                fn() => TestAttemptResponseResource::collection($this->responses)
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
