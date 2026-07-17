<?php
// app/Http/Controllers/Api/Settings/ProfileController.php
namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateProfileRequest;
use App\Http\Requests\Settings\UpdateAvatarRequest;
use App\Http\Resources\Settings\ProfileResource;
use App\Http\Resources\Settings\AvatarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * GET /api/settings/profile
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('details');

        return response()->json([
            'success' => true,
            'data'    => new ProfileResource($user),
        ]);
    }

    /**
     * PUT /api/settings/profile
     */
    public function update(UpdateProfileRequest $request)
    {
        $user      = $request->user();
        $validated = $request->validated();

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        $details = $user->details()->firstOrCreate(['user_id' => $user->id]);
        $details->update(['bio' => $validated['bio'] ?? $details->bio]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new ProfileResource($user->fresh('details')),
        ]);
    }

    /**
     * POST /api/settings/profile/avatar
     */
    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $user = $request->user();
        $disk = config('settings.avatar_disk', 'public');
        $path = config('settings.avatar_path', 'avatars');

        if ($user->avatar) {
            Storage::disk($disk)->delete($user->avatar);
        }

        $filename   = Str::uuid() . '.' . $request->file('avatar')->extension();
        $storedPath = $request->file('avatar')->storeAs($path, $filename, $disk);

        $user->update(['avatar' => $storedPath]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully.',
            'data'    => new AvatarResource($user->fresh()),
        ]);
    }
}
