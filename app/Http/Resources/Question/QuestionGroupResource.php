<?php
// app/Http/Resources/Question/GroupResource.php

namespace App\Http\Resources\Question;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_code' => $this->group_code,
            'questions_category' => $this->questions_category,
            'title' => $this->group_content['title'] ?? null,
            'content' => $this->group_content['content'] ?? null,
            'image' => $this->group_content['image'] ?? null,
        ];
    }
}
