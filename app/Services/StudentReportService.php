<?php
// app/Services/StudentReportService.php

namespace App\Services;

use App\Models\User;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\SubSkillType;
use App\Models\TestAttempt\TestAttempt;
use App\Models\TestAttempt\TestAttemptResponse;
use Illuminate\Support\Facades\DB;

class StudentReportService
{
    /**
     * Get report card data for a single student
     */
    public function getStudentReportCard($userId)
    {
        $user = User::with([
            'details.currentAgeGroup',
            'organisation',
            'completedTestAttempts.assessment',
            'completedTestAttempts.responses.assessmentQuestion.question.primarySkill',
            'completedTestAttempts.responses.assessmentQuestion.question.subSkill'
        ])->findOrFail($userId);

        $completedAttempts = $user->completedTestAttempts;

        if ($completedAttempts->isEmpty()) {
            return null; // Hide if no attempts
        }

        return [
            'student_id' => $user->details->student_id,
            'student_name' => $user->details->full_name ?: $user->name,
            'physical_age' => $user->details->physical_age,
            'mental_age_group' => $user->details->currentAgeGroup->name ?? 'Not Set',
            'iq_score' => $this->calculateIQScore($completedAttempts),
            'eq_score' => $this->calculateEQScore($completedAttempts),
            'lq_score' => $this->calculateLQScore($completedAttempts),
        ];
    }

    /**
     * Get detailed report for a student
     */
    public function getDetailedReport($userId)
    {
        $user = User::with([
            'details.currentAgeGroup',
            'organisation',
            'completedTestAttempts.assessment',
            'completedTestAttempts.responses.assessmentQuestion.question.primarySkill',
            'completedTestAttempts.responses.assessmentQuestion.question.subSkill'
        ])->findOrFail($userId);

        $completedAttempts = $user->completedTestAttempts;

        if ($completedAttempts->isEmpty()) {
            return null;
        }

        return [
            'student_name' => $user->details->full_name ?: $user->name,
            'institute_name' => $user->organisation->name ?? 'N/A',
            'physical_age' => $user->details->physical_age,
            'mental_age_group' => $user->details->currentAgeGroup->name ?? 'Not Set',
            'sub_skills' => $this->calculateSubSkillScores($completedAttempts),
        ];
    }

    /**
     * Calculate IQ Score (Cognitive Skill average)
     */
    private function calculateIQScore($attempts)
    {
        $cognitiveSkill = PrimarySkillType::where('slug', 'cognitive')->first();

        if (!$cognitiveSkill) {
            return 0;
        }

        return $this->calculateAverageScoreByPrimarySkill($attempts, $cognitiveSkill->id);
    }

    /**
     * Calculate EQ Score (Life Skill average)
     */
    private function calculateEQScore($attempts)
    {
        $lifeSkill = PrimarySkillType::where('slug', 'life')->first();

        if (!$lifeSkill) {
            return 0;
        }

        return $this->calculateAverageScoreByPrimarySkill($attempts, $lifeSkill->id);
    }

    /**
     * Calculate LQ Score (Leadership Skill average)
     */
    private function calculateLQScore($attempts)
    {
        $leadershipSkill = PrimarySkillType::where('slug', 'leadership')->first();

        if (!$leadershipSkill) {
            return 0;
        }

        return $this->calculateAverageScoreByPrimarySkill($attempts, $leadershipSkill->id);
    }

    /**
     * Calculate average score by primary skill across all attempts
     */
    private function calculateAverageScoreByPrimarySkill($attempts, $primarySkillId)
    {
        $totalMarks = 0;
        $obtainedMarks = 0;

        foreach ($attempts as $attempt) {
            foreach ($attempt->responses as $response) {
                $question = $response->assessmentQuestion->question;

                if ($question->primary_skill_id == $primarySkillId) {
                    $questionMarks = $question->marks;
                    $totalMarks += $questionMarks;
                    $obtainedMarks += $response->obtained_marks;
                }
            }
        }

        if ($totalMarks == 0) {
            return 0;
        }

        // Return percentage as raw score
        return round(($obtainedMarks / $totalMarks) * 100, 2);
    }

    /**
     * Calculate sub-skill wise scores (aggregate of all attempts)
     */
    private function calculateSubSkillScores($attempts)
    {
        $subSkillData = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->responses as $response) {
                $question = $response->assessmentQuestion->question;
                $subSkillId = $question->sub_skill_id;
                $subSkillName = $question->subSkill->name;

                if (!isset($subSkillData[$subSkillId])) {
                    $subSkillData[$subSkillId] = [
                        'name' => $subSkillName,
                        'correct' => 0,
                        'total' => 0,
                    ];
                }

                $subSkillData[$subSkillId]['total']++;

                if ($response->is_correct) {
                    $subSkillData[$subSkillId]['correct']++;
                }
            }
        }

        // Convert to array and sort by name
        $result = array_values($subSkillData);
        usort($result, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $result;
    }

    /**
     * Get all students report cards with pagination and filters
     */
    public function getAllStudentsReportCards($perPage = 15, $search = null, $ageGroupId = null)
    {
        $query = User::with([
            'details.currentAgeGroup',
            'organisation',
            'completedTestAttempts.assessment',
            'completedTestAttempts.responses.assessmentQuestion.question.primarySkill'
        ])
            ->whereHas('completedTestAttempts'); // Only users with completed attempts

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('details', function($ud) use ($search) {
                        $ud->where('student_id', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Age group filter
        if ($ageGroupId) {
            $query->whereHas('details', function($q) use ($ageGroupId) {
                $q->where('current_age_group_id', $ageGroupId);
            });
        }

        $users = $query->paginate($perPage);

        return $users->map(function($user) {
            return $this->getStudentReportCard($user->id);
        })->filter(); // Remove nulls
    }

    /**
     * Promote student to next age group if they passed
     */
    public function promoteStudentIfPassed($userId, $testAttemptId)
    {
        $attempt = TestAttempt::with('assessment.ageGroup', 'user.details')->findOrFail($testAttemptId);

        if ($attempt->isPassed()) {
            $currentAgeGroupId = $attempt->user->details->current_age_group_id;

            // Get next age group
            $nextAgeGroup = DB::table('age_groups')
                ->where('min_age', '>', $attempt->assessment->ageGroup->min_age)
                ->orderBy('min_age', 'asc')
                ->first();

            if ($nextAgeGroup) {
                $attempt->user->details->update([
                    'current_age_group_id' => $nextAgeGroup->id
                ]);

                return true;
            }
        }

        return false;
    }
}
