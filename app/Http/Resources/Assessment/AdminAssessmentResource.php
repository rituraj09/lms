<?php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminAssessmentResource extends JsonResource
{
   // AdminAssessmentResource.php

    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'assessment_code'     => $this->assessment_code,
            'title'               => $this->title,
            'instructions'        => $this->instructions,
            'status'              => $this->status,
            'assessment_type_id'  => $this->assessment_type_id,

            'assessment_type'     => $this->whenLoaded('assessmentType', fn() => [
                'id'   => $this->assessmentType->id,
                'code' => $this->assessmentType->code ?? null,
                'name' => $this->assessmentType->name ?? null,
            ]),

            // ✅ Fallback to null string so frontend always gets a value
            'age_group'           => $this->whenLoaded(
                'ageGroup',
                fn() => $this->ageGroup?->name ?? null,
                null  // default if NOT loaded
            ),

            'difficulty_level'    => $this->whenLoaded(
                'difficultyLevel',
                fn() => $this->difficultyLevel?->level ?? null,
                null
            ),

            'difficulty_name'     => $this->whenLoaded(
                'difficultyLevel',
                fn() => $this->difficultyLevel?->name ?? null,
                null
            ),

            'total_marks'         => (float) $this->total_marks,
            'passing_marks'       => (float) $this->passing_marks,
            'duration_minutes'    => $this->duration_minutes,
            'has_negative_mark'   => $this->has_negative_mark,
            'max_attempts'        => $this->max_attempts ?? 1,

            // ✅ total_questions_count comes from withCount('questions')
            'total_questions'     => $this->total_questions_count ?? 0,

            'created_at'          => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'          => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
