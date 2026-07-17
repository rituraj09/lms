<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'badge_url' => $this->badge ? asset('storage/' . $this->badge) : null,
            'age_group' => $this->whenLoaded('ageGroup', function() {
                return $this->ageGroup->name;
            }),
            'difficulty_level' => $this->whenLoaded('difficultyLevel', function() {
                return $this->difficultyLevel->name;
            }),
        ];
    }
}
