<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\AssessmentMaster\Assessment;
use App\Models\AssessmentMaster\AssessmentGroup;
use App\Models\AssessmentMaster\AssessmentQuestion;
use App\Models\QuestionMaster\QuestionGroup;
use App\Models\QuestionMaster\Question;
use App\Models\User;
use App\Models\TestAttempt\TestAttempt;
use App\Models\TestAttempt\TestAttemptResponse;
use App\Models\Master\Organisation;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\DifficultyLevel;
use App\Models\EvaluationMaster\AgeGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.backend')]
class Dashboard extends Component
{
    public function mount()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
    }

    // ══════════════════════════════════════════════════════════
    // ASSESSMENT METRICS
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function totalAssessments()
    {
        return Assessment::count();
    }

    #[Computed]
    public function publishedAssessments()
    {
        return Assessment::where('status', 'publish')->count();
    }

    #[Computed]
    public function draftAssessments()
    {
        return Assessment::where('status', 'draft')->count();
    }

    #[Computed]
    public function unpublishedAssessments()
    {
        return Assessment::where('status', 'unpublish')->count();
    }

    // ══════════════════════════════════════════════════════════
    // QUESTION METRICS
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function totalQuestions()
    {
        return Question::count();
    }

    #[Computed]
    public function totalQuestionGroups()
    {
        return QuestionGroup::count();
    }

    #[Computed]
    public function questionsByAnswerCategory()
    {
        return Question::selectRaw('answer_category, COUNT(*) as count')
            ->groupBy('answer_category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->answer_category,
                    'count' => $item->count,
                    'percentage' => $this->totalQuestions > 0
                        ? round(($item->count / $this->totalQuestions) * 100, 1)
                        : 0
                ];
            });
    }

    #[Computed]
    public function questionsByDifficulty()
    {
        return Question::with('difficultyLevel')
            ->selectRaw('difficulty_level_id, COUNT(*) as count')
            ->groupBy('difficulty_level_id')
            ->get()
            ->map(function ($item) {
                return [
                    'difficulty' => $item->difficultyLevel->name ?? 'Unknown',
                    'count' => $item->count,
                    'percentage' => $this->totalQuestions > 0
                        ? round(($item->count / $this->totalQuestions) * 100, 1)
                        : 0
                ];
            });
    }

    #[Computed]
    public function questionsBySkill()
    {
        return Question::with('primarySkill')
            ->selectRaw('primary_skill_id, COUNT(*) as count')
            ->groupBy('primary_skill_id')
            ->limit(5)
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'skill' => $item->primarySkill->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });
    }

    // ══════════════════════════════════════════════════════════
    // STUDENT & ORGANISATION METRICS
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function totalStudents()
    {
        return User::count();
    }

    #[Computed]
    public function activeStudents()
    {
        return User::where('status', 'active')->count();
    }

    #[Computed]
    public function totalOrganisations()
    {
        return Organisation::count();
    }

    #[Computed]
    public function activeOrganisations()
    {
        return Organisation::where('status', 'active')->count();
    }

    #[Computed]
    public function topOrganisations()
    {
        return Organisation::withCount('admins')

            ->limit(5)
            ->get()
            ->map(function ($org) {
                return [
                    'name' => $org->name,
                    'students' => $org->users_count,
                    'max_students' => $org->max_students
                ];
            });
    }

    // ══════════════════════════════════════════════════════════
    // TEST ATTEMPT METRICS
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function totalAttempts()
    {
        return TestAttempt::count();
    }

    #[Computed]
    public function submittedAttempts()
    {
        return TestAttempt::where('status', 'submitted')->count();
    }

    #[Computed]
    public function evaluatedAttempts()
    {
        return TestAttempt::where('status', 'evaluated')->count();
    }

    #[Computed]
    public function inProgressAttempts()
    {
        return TestAttempt::where('status', 'in_progress')->count();
    }

    #[Computed]
    public function attemptsByStatus()
    {
        return TestAttempt::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => $item->count,
                    'percentage' => $this->totalAttempts > 0
                        ? round(($item->count / $this->totalAttempts) * 100, 1)
                        : 0
                ];
            });
    }

    #[Computed]
    public function averageScore()
    {
        return round(TestAttempt::where('status', 'evaluated')->avg('total_score') ?? 0, 2);
    }

    #[Computed]
    public function totalResponses()
    {
        return TestAttemptResponse::count();
    }

    #[Computed]
    public function correctResponses()
    {
        return TestAttemptResponse::where('is_correct', true)->count();
    }

    #[Computed]
    public function accuracyRate()
    {
        return $this->totalResponses > 0
            ? round(($this->correctResponses / $this->totalResponses) * 100, 1)
            : 0;
    }

    // ══════════════════════════════════════════════════════════
    // ASSESSMENT ANALYTICS
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function assessmentsByType()
    {
        return Assessment::selectRaw('assessment_type_id, COUNT(*) as count')
            ->groupBy('assessment_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->assessment_type_id,
                    'count' => $item->count,
                    'percentage' => $this->totalAssessments > 0
                        ? round(($item->count / $this->totalAssessments) * 100, 1)
                        : 0
                ];
            });
    }

    #[Computed]
    public function assessmentsByStatus()
    {
        return Assessment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => $item->count,
                    'percentage' => $this->totalAssessments > 0
                        ? round(($item->count / $this->totalAssessments) * 100, 1)
                        : 0
                ];
            });
    }

    #[Computed]
    public function assessmentsByAgeGroup()
    {
        return Assessment::with('ageGroup')
            ->selectRaw('age_group_id, COUNT(*) as count')
            ->groupBy('age_group_id')
            ->get()
            ->map(function ($item) {
                return [
                    'age_group' => $item->ageGroup->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });
    }

    #[Computed]
    public function recentAssessments()
    {
        return Assessment::with('ageGroup')
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function mostAttemptedAssessments()
    {
        return Assessment::withCount('testAttempts')
            ->orderByDesc('test_attempts_count')
            ->limit(5)
            ->get()
            ->map(function ($assessment) {
                return [
                    'title' => $assessment->title,
                    'code' => $assessment->assessment_code,
                    'attempts' => $assessment->test_attempts_count
                ];
            });
    }

    // ══════════════════════════════════════════════════════════
    // TRENDS & STATISTICS
    // ══════════════════════════════════════════════════════════

    #[Computed]
    public function assessmentTrend()
    {
        $lastMonth = now()->subMonth();

        return Assessment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $lastMonth)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count,
                ];
            });
    }

    #[Computed]
    public function attemptTrend()
    {
        $lastMonth = now()->subMonth();

        return TestAttempt::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $lastMonth)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count,
                ];
            });
    }

    #[Computed]
    public function recentAttempts()
    {
        return TestAttempt::with(['user', 'assessment'])
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function assessmentStatistics()
    {
        $totalDuration = Assessment::sum('duration_minutes');
        $avgDuration = Assessment::avg('duration_minutes');
        $totalMarks = Assessment::sum('total_marks');
        $avgMarks = Assessment::avg('total_marks');

        return [
            'totalDuration' => $totalDuration ?? 0,
            'avgDuration' => round($avgDuration ?? 0, 2),
            'totalMarks' => round($totalMarks ?? 0, 2),
            'avgMarks' => round($avgMarks ?? 0, 2),
        ];
    }

    #[Computed]
    public function questionGroupsCount()
    {
        return QuestionGroup::selectRaw('questions_category, COUNT(*) as count')
            ->groupBy('questions_category')
            ->get();
    }

    // ══════════════════════════════════════════════════════════
    // HELPER METHODS
    // ══════════════════════════════════════════════════════════

    public function getTypeColor(string $type): string
    {
        return match($type) {
            'iq' => '#4361ee',
            'eq' => '#f77f00',
            'lq' => '#d62828',
            'iq+eq' => '#06a77d',
            'iq+lq' => '#8338ec',
            'eq+lq' => '#ffbe0b',
            'iq+eq+lq' => '#fb5607',
            default => '#adb5bd'
        };
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'publish' => '#06a77d',
            'draft' => '#f77f00',
            'unpublish' => '#d62828',
            'active' => '#06a77d',
            'inactive' => '#d62828',
            'suspended' => '#8338ec',
            'in_progress' => '#f77f00',
            'submitted' => '#4361ee',
            'evaluated' => '#06a77d',
            default => '#adb5bd'
        };
    }

    public function getDifficultyColor(string $difficulty): string
    {
        return match(strtolower($difficulty)) {
            'easy' => '#06a77d',
            'medium' => '#f77f00',
            'hard' => '#d62828',
            default => '#adb5bd'
        };
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
