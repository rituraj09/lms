<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name'               => $this->first_name,
            'last_name'                => $this->last_name,
            'gender'                   => $this->gender,
            'date_of_birth'            => $this->date_of_birth?->format('Y-m-d'),
            'age'                      => $this->age,
            'address_line1'            => $this->address_line1,
            'address_line2'            => $this->address_line2,
            'city'                     => $this->city,
            'state'                    => $this->state,
            'country'                  => $this->country,
            'postal_code'              => $this->postal_code,
            'full_address'             => $this->full_address,
            'emergency_contact_name'   => $this->emergency_contact_name,
            'emergency_contact_phone'  => $this->emergency_contact_phone,
            'bio'                      => $this->bio,
            'extra_data'               => $this->extra_data,
        ];
    }
}
