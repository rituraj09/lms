<?php
// app/Http/Resources/Settings/PrivacyResource.php
namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $defaults = config('settings.privacy_defaults', []);
        $stored   = $this->resource['privacy'] ?? [];
        $merged   = array_merge($defaults, $stored);

        return [
            'profile_visibility'  => $merged['profile_visibility']  ?? 'public',
            'show_on_leaderboard' => (bool) ($merged['show_on_leaderboard'] ?? true),
            'two_factor_enabled'  => (bool) ($merged['two_factor_enabled']  ?? false),
        ];
    }
}
