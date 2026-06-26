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
            'id'                      => $this->id,
            'assessment_code'         => $this->assessment_code,
            'title'                   => $this->title,
            'cover_image'             => $this->cover_image
                ? url('storage/' . $this->cover_image)
                : null,
            'instructions'            => $this->instructions,
            'assessment_type_id'      => $this->assessment_type_id,
            'total_marks'             => (float) $this->total_marks,
            'passing_marks'           => (float) $this->passing_marks,
            'duration_minutes'        => (int) $this->duration_minutes,
            'has_negative_mark'       => (bool) $this->has_negative_mark,
            'status'                  => $this->status,

            // ── New fields ──────────────────────────────────────
            'max_attempts'            => (int)  $this->max_attempts,
            'shuffle_sections'        => (bool) $this->shuffle_sections,
            'show_result_immediately' => (bool) $this->show_result_immediately,
            'show_correct_answers'    => (bool) $this->show_correct_answers,
            'show_explainations'      => (bool) $this->show_explainations,
            // ────────────────────────────────────────────────────

            'age_group'               => $this->whenLoaded('ageGroup', fn() => [
                'id'   => $this->ageGroup->id,
                'name' => $this->ageGroup->name,
            ]),
            'total_questions'         => $totalQuestions,
            'assessment_groups'       => $this->whenLoaded(
                'assessmentGroups',
                fn() => AssessmentGroupResource::collection($this->assessmentGroups)
            ),
        ];
    }
}
