<?php
// app/Http/Resources/AssessmentDetailResource.php

namespace App\Http\Resources\Assessment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Calculate total questions safely
        $totalQuestions = 0;
        try {
            if ($this->relationLoaded('assessmentGroups')) {
                $totalQuestions = $this->assessmentGroups
                    ->sum(fn($g) => $g->relationLoaded('assessmentQuestions')
                        ? $g->assessmentQuestions->count()
                        : 0
                    );
            }
            // Also check for pre-computed attribute
            if (isset($this->total_questions_count)) {
                $totalQuestions = $this->total_questions_count;
            }
        } catch (\Throwable $e) {
            $totalQuestions = 0;
        }

        return [
            'id'                 => $this->id,
            'assessment_code'    => $this->assessment_code,
            'title'              => $this->title,
            'instructions'       => $this->instructions,
            'assessment_type_id' => $this->assessment_type_id,
            'total_marks'        => (float) $this->total_marks,
            'passing_marks'      => (float) $this->passing_marks,
            'duration_minutes'   => (int) $this->duration_minutes,
            'has_negative_mark'  => (bool) $this->has_negative_mark,
            'status'             => $this->status,
            'age_group'          => $this->whenLoaded('ageGroup', fn() => [
                'id'   => $this->ageGroup->id,
                'name' => $this->ageGroup->name,
            ]),
            'total_questions'    => $totalQuestions,
            'assessment_groups'  => $this->whenLoaded(
                'assessmentGroups',
                fn() => AssessmentGroupResource::collection($this->assessmentGroups)
            ),
        ];
    }
}
