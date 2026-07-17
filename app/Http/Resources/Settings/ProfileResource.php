<?php
// app/Http/Resources/Settings/ProfileResource.php
namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'avatar_url' => $this->avatar_url,
            'role'       => $this->role,
            'status'     => $this->status,
            'is_active'  => $this->is_active,
            'details'    => $this->when(
                $this->relationLoaded('details') && $this->details,
                fn() => [
                    'first_name' => $this->details->first_name,
                    'last_name'  => $this->details->last_name,
                    'full_name'  => $this->details->full_name,
                    'bio'        => $this->details->bio,
                ]
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
