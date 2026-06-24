<?php
// app/Http/Resources/TestAttempt/TestAttemptResource.php

namespace App\Http\Resources\TestAttempt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ack_no' => $this->ack_no,
            'assessment_id' => $this->assessment_id,
            'user_id' => $this->user_id,
            'started_at' => $this->started_at?->toIso8601String(),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'negative_score' => (float) $this->negative_score,
            'total_score' => (float) $this->total_score,
            'status' => $this->status,

            // Computed fields
            'total_questions' => $this->getTotalQuestionsCount(),
            'answered_questions' => $this->getAnsweredQuestionsCount(),
            'time_elapsed' => $this->getTimeElapsed(),
            'time_remaining' => $this->getTimeRemaining(),
            'duration_minutes' => $this->getDurationInMinutes(),
            'has_expired' => $this->hasExpired(),

            // Relationships
            'assessment' => new AssessmentBasicResource($this->whenLoaded('assessment')),
            'user' => new UserBasicResource($this->whenLoaded('user')),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
