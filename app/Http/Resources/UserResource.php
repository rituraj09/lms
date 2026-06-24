<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'status'          => $this->status,
            'is_active'       => $this->is_active,
            'avatar_url'      => $this->avatar_url,
            'full_name'       => $this->full_name,
            'student_id'      => $this->student_id,
            'organisation_id' => $this->organisation_id,
            'details'         => $this->whenLoaded('details'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
