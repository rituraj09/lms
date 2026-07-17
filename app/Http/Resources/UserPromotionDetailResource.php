<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPromotionDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $testAttempt = $this->whenLoaded('testAttempt');
        $assessment = $testAttempt?->assessment ?? null;

        $percentage = 0;
        if ($testAttempt && $assessment && $assessment->total_marks > 0) {
            $percentage = round(($testAttempt->total_score / $assessment->total_marks) * 100, 1);
        }

        return [
            'id' => $this->id,
            'assessment_type' => $this->assessment_type,
            'current_status' => (bool) $this->current_status,
            'promotion_detail' => new PromotionDetailResource($this->whenLoaded('promotionDetail')),
            'test_attempt' => $this->when($testAttempt, function() use ($testAttempt, $percentage) {
                return [
                    'id' => $testAttempt->id,
                    'ack_no' => $testAttempt->ack_no,
                    'total_score' => (float) $testAttempt->total_score,
                    'negative_score' => (float) $testAttempt->negative_score,
                    'submitted_at' => $testAttempt->submitted_at?->format('Y-m-d H:i:s'),
                    'percentage' => $percentage,
                    'assessment' => [
                        'name' => $testAttempt->assessment->name ?? null,
                        'total_marks' => (float) ($testAttempt->assessment->total_marks ?? 0),
                        'assessment_type' => $testAttempt->assessment->assessment_type ?? null,
                    ],
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
