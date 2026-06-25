<?php
// app/Http/Resources/TestAttemptListResource.php

namespace App\Http\Resources\TestAttempt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Safely get total questions
        $totalQuestions = 0;
        try {
            if ($this->assessment) {
                $totalQuestions = $this->assessment->assessmentGroups
                    ->sum(fn($g) => $g->assessmentQuestions->count());
            }
        } catch (\Throwable $e) {
            $totalQuestions = 0;
        }

        // Safely get answered questions
        $answeredQuestions = 0;
        try {
            $answeredQuestions = $this->responses()
                ->whereNotNull('response')
                ->count();
        } catch (\Throwable $e) {
            $answeredQuestions = 0;
        }

        return [
            'id'                 => $this->id,
            'ack_no'             => $this->ack_no,
            'assessment_id'      => $this->assessment_id,
            'assessment_title'   => $this->assessment->title ?? '',
            'assessment_code'    => $this->assessment->assessment_code ?? '',
            'started_at'         => $this->started_at?->toIso8601String(),
            'submitted_at'       => $this->submitted_at?->toIso8601String(),
            'total_score'        => (float) $this->total_score,
            'status'             => $this->status,
            'total_questions'    => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}
