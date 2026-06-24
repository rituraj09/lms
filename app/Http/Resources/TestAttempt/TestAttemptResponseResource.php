<?php
// app/Http/Resources/TestAttempt/TestAttemptResponseResource.php

namespace App\Http\Resources\TestAttempt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestAttemptResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'test_attempt_id' => $this->test_attempt_id,
            'assessment_question_id' => $this->assessment_question_id,
            'response' => $this->response,
            'obtained_marks' => (float) $this->obtained_marks,
            'is_correct' => $this->is_correct,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
