<?php
// app/Http/Resources/Settings/NotificationPreferenceResource.php
namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationPreferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $defaults = config('settings.notification_defaults', []);
        $stored   = $this->resource['notifications'] ?? [];
        $merged   = array_merge($defaults, $stored);

        return [
            'course_updates'      => (bool) ($merged['course_updates']      ?? true),
            'exam_reminders'      => (bool) ($merged['exam_reminders']       ?? true),
            'achievement_alerts'  => (bool) ($merged['achievement_alerts']   ?? true),
            'leaderboard_changes' => (bool) ($merged['leaderboard_changes']  ?? false),
            'weekly_digest'       => (bool) ($merged['weekly_digest']        ?? true),
            'marketing_emails'    => (bool) ($merged['marketing_emails']     ?? false),
        ];
    }
}
