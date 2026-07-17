<?php
// app/Http/Controllers/Api/LeaderboardController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(private LeaderboardService $leaderboardService) {}

    /**
     * GET /api/leaderboard?period=all|monthly|weekly&limit=50
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'period' => 'in:all,monthly,weekly',
            'limit'  => 'nullable|integer|min:1|max:100',
        ]);

        $period = $validated['period'] ?? 'all';
        $limit  = $validated['limit'] ?? 50;

        $user = $request->user();

        $data = $this->leaderboardService->getLeaderboard(
            $period,
            $limit,
            $user->organisation_id ?? null
        );

        return response()->json([
            'success' => true,
            'period'  => $period,
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/leaderboard/me?period=all|monthly|weekly
     */
    public function me(Request $request)
    {
        $validated = $request->validate(['period' => 'in:all,monthly,weekly']);
        $period = $validated['period'] ?? 'all';

        $stats = $this->leaderboardService->getMyStats($request->user(), $period);

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}
