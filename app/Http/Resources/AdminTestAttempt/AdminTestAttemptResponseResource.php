<?php
// app/Http/Resources/AdminTestAttempt/AdminTestAttemptResponseResource.php

namespace App\Http\Resources\AdminTestAttempt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminTestAttemptResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'admin_test_attempt_id'   => $this->admin_test_attempt_id,
            'assessment_question_id'  => $this->assessment_question_id,
            'response'                => $this->response,
            'obtained_marks'          => (float) $this->obtained_marks,
            'is_correct'              => $this->is_correct,
            'created_at'              => $this->created_at?->toIso8601String(),
            'updated_at'              => $this->updated_at?->toIso8601String(),
        ];
    }
}
