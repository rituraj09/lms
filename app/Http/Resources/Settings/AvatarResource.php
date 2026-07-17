<?php
// app/Http/Resources/Settings/AvatarResource.php
namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvatarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'avatar_url' => $this->avatar_url,
        ];
    }
}
