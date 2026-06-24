<?php
// app/Http/Resources/Assessment/AssessmentDetailResource.php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_code' => $this->assessment_code,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'assessment_type_id' => $this->assessment_type_id,
            'total_marks' => (float) $this->total_marks,
            'passing_marks' => (float) $this->passing_marks,
            'duration_minutes' => $this->duration_minutes,
            'has_negative_mark' => $this->has_negative_mark,
            'status' => $this->status,
            'age_group' => [
                'id' => $this->ageGroup->id ?? null,
                'name' => $this->ageGroup->name ?? null,
            ],
            'total_questions' => $this->whenCounted('assessmentGroups'),
            'assessment_groups' => AssessmentGroupResource::collection($this->whenLoaded('assessmentGroups')),
        ];
    }
}
