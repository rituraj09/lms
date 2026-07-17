<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'percentage_min' => (float) $this->percentage_min,
            'percentage_max' => (float) $this->percentage_max,
            'current_promotion' => new PromotionResource($this->whenLoaded('currentPromotion')),
            'next_promotion' => new PromotionResource($this->whenLoaded('nextPromotion')),
        ];
    }
}
