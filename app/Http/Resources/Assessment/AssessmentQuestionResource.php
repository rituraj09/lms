<?php
// app/Http/Resources/Assessment/AssessmentQuestionResource.php

namespace App\Http\Resources\Assessment;

use App\Http\Resources\Question\QuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_group_id' => $this->assessment_group_id,
            'question_id' => $this->question_id,
            'negative_mark' => (float) $this->negative_mark,
            'question_timer' => $this->question_timer,
            'question_type_id' => $this->question_type_id,
            'question' => new QuestionResource($this->whenLoaded('question')),
        ];
    }
}
