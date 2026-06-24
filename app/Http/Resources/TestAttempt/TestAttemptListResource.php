<?php
// app/Http/Resources/TestAttempt/TestAttemptListResource.php

namespace App\Http\Resources\TestAttempt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ack_no' => $this->ack_no,
            'assessment_id' => $this->assessment_id,
            'assessment_title' => $this->assessment->title ?? null,
            'assessment_code' => $this->assessment->assessment_code ?? null,
            'started_at' => $this->started_at?->toIso8601String(),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'total_score' => (float) $this->total_score,
            'status' => $this->status,
            'total_questions' => $this->getTotalQuestionsCount(),
            'answered_questions' => $this->getAnsweredQuestionsCount(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
