<?php
// app/Http/Controllers/Api/Settings/PreferenceController.php
namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateNotificationPreferencesRequest;
use App\Http\Requests\Settings\UpdatePrivacyRequest;
use App\Http\Resources\Settings\NotificationPreferenceResource;
use App\Http\Resources\Settings\PrivacyResource;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    /**
     * GET /api/settings/notifications
     */
    public function notifications(Request $request)
    {
        $settings = $request->user()->details?->extra_data['settings'] ?? [];

        return response()->json([
            'success' => true,
            'data'    => new NotificationPreferenceResource($settings),
        ]);
    }

    /**
     * PUT /api/settings/notifications
     */
    public function updateNotifications(UpdateNotificationPreferencesRequest $request)
    {
        $user    = $request->user();
        $details = $user->details()->firstOrCreate(['user_id' => $user->id]);

        $extra   = $details->extra_data ?? [];
        $current = $extra['settings']['notifications'] ?? [];

        $extra['settings']['notifications'] = array_merge($current, $request->validated());

        $details->update(['extra_data' => $extra]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated.',
            'data'    => new NotificationPreferenceResource($extra['settings']),
        ]);
    }

    /**
     * GET /api/settings/privacy
     */
    public function privacy(Request $request)
    {
        $settings = $request->user()->details?->extra_data['settings'] ?? [];

        return response()->json([
            'success' => true,
            'data'    => new PrivacyResource($settings),
        ]);
    }

    /**
     * PUT /api/settings/privacy
     */
    public function updatePrivacy(UpdatePrivacyRequest $request)
    {
        $user    = $request->user();
        $details = $user->details()->firstOrCreate(['user_id' => $user->id]);

        $extra   = $details->extra_data ?? [];
        $current = $extra['settings']['privacy'] ?? [];

        $extra['settings']['privacy'] = array_merge($current, $request->validated());

        $details->update(['extra_data' => $extra]);

        return response()->json([
            'success' => true,
            'message' => 'Privacy settings updated.',
            'data'    => new PrivacyResource($extra['settings']),
        ]);
    }
}
