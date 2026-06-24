<?php
// app/Http/Resources/Question/QuestionResource.php

namespace App\Http\Resources\Question;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $content = $this->question_content ?? [];

        return [
            'id' => $this->id,
            'question_code' => $this->question_code,
            'answer_category' => $this->answer_category,
            'stem' => $content['stem'] ?? [],
            'image' => $content['image'] ?? null,
            'marks' => $content['marks'] ?? 0,
            'options' => $content['options'] ?? [],
            // Don't send correct answers or explanations during attempt
            // 'explanation' => $content['explanation'] ?? [],
        ];
    }
}
