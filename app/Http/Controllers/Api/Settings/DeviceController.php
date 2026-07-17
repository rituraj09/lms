<?php
// app/Http/Controllers/Api/Settings/DeviceController.php
namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\DeviceCollection;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * GET /api/settings/devices
     */
    public function index(Request $request)
    {
        $user           = $request->user();
        $currentTokenId = (int) $user->currentAccessToken()?->id;

        $tokens = $user->tokens()->orderByDesc('last_used_at')->get();

        return response()->json([
            'success' => true,
            'data'    => new DeviceCollection($tokens, $currentTokenId),
        ]);
    }

    /**
     * DELETE /api/settings/devices/{tokenId}
     */
    public function revoke(Request $request, int $tokenId)
    {
        $user           = $request->user();
        $currentTokenId = (int) $user->currentAccessToken()?->id;

        if ($tokenId === $currentTokenId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot revoke your current session.',
            ], 422);
        }

        $deleted = $user->tokens()->where('id', $tokenId)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device signed out successfully.',
        ]);
    }

    /**
     * POST /api/settings/devices/revoke-all
     */
    public function revokeAll(Request $request)
    {
        $user           = $request->user();
        $currentTokenId = (int) $user->currentAccessToken()?->id;

        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All other devices signed out successfully.',
        ]);
    }
}
