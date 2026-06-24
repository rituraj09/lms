<?php
// app/Http/Resources/Assessment/AssessmentGroupResource.php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_id' => $this->assessment_id,
            'question_group_id' => $this->question_group_id,
            'instructions' => $this->instructions,
            'suffle_question' => $this->suffle_question,
            'allow_back_to_group_question' => $this->allow_back_to_group_question,
            'allow_back_to_previous_question' => $this->allow_back_to_previous_question,
            'group_timer' => $this->group_timer,
            'question_group' => new QuestionGroupResource($this->whenLoaded('questionGroup')),
            'assessment_questions' => AssessmentQuestionResource::collection($this->whenLoaded('assessmentQuestions')),
        ];
    }
}
