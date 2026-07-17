<?php
// app/Http/Resources/Settings/DeviceResource.php
namespace App\Http\Resources\Settings;

use App\Support\AgentParser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public int $currentTokenId;

    public function toArray(Request $request): array
    {
        $parsed = AgentParser::parse($this->name ?? '');

        return [
            'id'          => $this->id,
            'name'        => $parsed['device'],
            'type'        => $parsed['type'],
            'is_current'  => $this->id === $this->currentTokenId,
            'last_active' => optional($this->last_used_at ?? $this->created_at)->diffForHumans(),
        ];
    }
}
