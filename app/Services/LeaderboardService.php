<?php
// app/Services/LeaderboardService.php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    private float $passRatio;
    private int $examBonus;
    private int $levelThreshold;

    public function __construct()
    {
        $this->passRatio = (float) config('gamification.pass_ratio', 0.40);
        $this->examBonus = (int) config('gamification.points.exam_pass_bonus', 50);
        $this->levelThreshold = (int) config('gamification.level_threshold', 500);
    }

    /**
     * Top N leaderboard for a given period, optionally scoped to an organisation.
     * Returns array of ['rank','user','points','level','streak','courses_completed']
     */
    public function getLeaderboard(string $period = 'all', int $limit = 50, ?int $organisationId = null): array
    {
        $cacheKey = "leaderboard:{$period}:{$organisationId}:{$limit}";
        $ttl = (int) config('gamification.cache_ttl_seconds', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($period, $limit, $organisationId) {
            $rows = $this->pointsQuery($period, $organisationId)
                ->orderByDesc('points')
                ->limit($limit)
                ->get();

            if ($rows->isEmpty()) {
                return [];
            }

            $userIds = $rows->pluck('user_id')->all();
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            $streaks = $this->getStreaksForUsers($userIds);

            $result = [];
            $rank = 1;

            foreach ($rows as $row) {
                $user = $users->get($row->user_id);
                if (!$user) continue;

                $result[] = [
                    'rank'              => $rank++,
                    'user'              => [
                        'id'     => $user->id,
                        'name'   => $user->name,
                        'avatar' => $user->avatar_url,
                    ],
                    'points'            => (int) $row->points,
                    'level'             => $this->calculateLevel((int) $row->points),
                    'streak'            => $streaks[$user->id] ?? 0,
                    'courses_completed' => (int) $row->courses_completed,
                ];
            }

            return $result;
        });
    }

    /**
     * Single user's stats + computed rank (not cached — always fresh for "me").
     */
    public function getMyStats(User $user, string $period = 'all'): array
    {
        $myRow = $this->pointsQuery($period)
            ->where('ta.user_id', $user->id)
            ->first();

        $points = (int) ($myRow->points ?? 0);
        $coursesCompleted = (int) ($myRow->courses_completed ?? 0);

        $rank = null;
        if ($points > 0) {
            $higherCount = $this->pointsQuery($period)
                ->havingRaw('SUM(ROUND(ta.total_score) + CASE WHEN ta.total_score >= (a.total_marks * ?) THEN ? ELSE 0 END) > ?', [
                    $this->passRatio, $this->examBonus, $points,
                ])
                ->get()
                ->count();

            $rank = $higherCount + 1;
        }

        $streaks = $this->getStreaksForUsers([$user->id]);

        return [
            'rank'              => $rank,
            'points'            => $points,
            'level'             => $this->calculateLevel($points),
            'streak'            => $streaks[$user->id] ?? 0,
            'courses_completed' => $coursesCompleted,
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'avatar' => $user->avatar_url,
            ],
        ];
    }

    /**
     * Base aggregate query: points + courses_completed per user, from test_attempts.
     */
    private function pointsQuery(string $period, ?int $organisationId = null)
    {
        $query = DB::table('test_attempts as ta')
            ->join('assessments as a', 'a.id', '=', 'ta.assessment_id')
            ->join('users as u', 'u.id', '=', 'ta.user_id')
            ->where('ta.status', 'evaluated')
            ->when($organisationId, fn ($q) => $q->where('u.organisation_id', $organisationId))
            ->when($period === 'weekly', fn ($q) => $q->where('ta.submitted_at', '>=', Carbon::now()->startOfWeek()))
            ->when($period === 'monthly', fn ($q) => $q->where('ta.submitted_at', '>=', Carbon::now()->startOfMonth()))
            ->groupBy('ta.user_id')
            ->select([
                'ta.user_id',
                DB::raw('SUM(ROUND(ta.total_score) + CASE WHEN ta.total_score >= (a.total_marks * ' . $this->passRatio . ') THEN ' . $this->examBonus . ' ELSE 0 END) as points'),
                DB::raw('COUNT(DISTINCT CASE WHEN ta.total_score >= (a.total_marks * ' . $this->passRatio . ') THEN ta.assessment_id END) as courses_completed'),
            ]);

        return $query;
    }

    /**
     * Compute current daily streak for a set of users, purely from submitted_at dates.
     * Returns [user_id => streak_days]
     */
    private function getStreaksForUsers(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        $rows = DB::table('test_attempts')
            ->whereIn('user_id', $userIds)
            ->where('status', 'evaluated')
            ->whereNotNull('submitted_at')
            ->selectRaw('user_id, DATE(submitted_at) as day')
            ->groupBy('user_id', DB::raw('DATE(submitted_at)'))
            ->orderBy('user_id')
            ->orderByDesc('day')
            ->get()
            ->groupBy('user_id');

        $streaks = [];
        $today = Carbon::today();

        foreach ($rows as $userId => $days) {
            $dates = $days->pluck('day')->map(fn ($d) => Carbon::parse($d))->values();

            if ($dates->isEmpty()) {
                $streaks[$userId] = 0;
                continue;
            }

            // streak only "alive" if last activity was today or yesterday
            $mostRecent = $dates->first();
            if ($mostRecent->diffInDays($today) > 1) {
                $streaks[$userId] = 0;
                continue;
            }

            $streak = 1;
            for ($i = 0; $i < $dates->count() - 1; $i++) {
                $diff = $dates[$i]->diffInDays($dates[$i + 1]);
                if ($diff === 1) {
                    $streak++;
                } else {
                    break;
                }
            }

            $streaks[$userId] = $streak;
        }

        return $streaks;
    }

    public function calculateLevel(int $points): int
    {
        return intdiv(max(0, $points), $this->levelThreshold) + 1;
    }
}
