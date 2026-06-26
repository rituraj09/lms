<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestAttempt\TestAttemptResponseResource;
use App\Http\Resources\TestAttempt\TestAttemptResource;
use App\Http\Resources\TestAttempt\TestAttemptListResource;
use App\Http\Resources\Assessment\AssessmentDetailResource;
use App\Models\TestAttempt\TestAttempt;
use App\Models\TestAttempt\TestAttemptResponse;
use App\Models\AssessmentMaster\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TestAttemptController extends Controller
{
    /**
     * Get all assessments assigned to user's organization
     */
    public function getAssignedAssessments(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->organisation_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not assigned to any organization'
                ], 403);
            }

            $assessments = Assessment::whereHas('organisations', function ($query) use ($user) {
                $query->where('organisations.id', $user->organisation_id)
                    ->where('assessment_organisation.status', 'active');
            })
                ->where('status', 'publish')
                ->with([
                    'ageGroup',
                    'assessmentGroups.assessmentQuestions',
                ])
                ->get();

            // Calculate total_questions for each assessment
            $assessments->each(function ($assessment) {
                $assessment->total_questions_count = $assessment->assessmentGroups
                    ->sum(function ($group) {
                        return $group->assessmentQuestions->count();
                    });
            });

            return response()->json([
                'success' => true,
                'data'    => AssessmentDetailResource::collection($assessments),
            ]);

        } catch (\Throwable $e) {
            Log::error('getAssignedAssessments failed', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessments',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get user's test attempts (no pagination — flat array)
     */
    public function myAttempts(Request $request)
    {
        try {
            $user = $request->user();

            $attempts = TestAttempt::where('user_id', $user->id)
                ->with(['assessment'])
                ->latest()
                ->get();  // ← get() not paginate()

            return response()->json([
                'success' => true,
                'data'    => TestAttemptListResource::collection($attempts),
            ]);

        } catch (\Throwable $e) {
            Log::error('myAttempts failed', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attempts',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get assessment detail with cover page info
     */
    public function getAssessmentDetail(Request $request, int $assessmentId)
    {
        try {
            $user = $request->user();

            $assessment = Assessment::whereHas('organisations', function ($query) use ($user) {
                $query->where('organisations.id', $user->organisation_id)
                    ->where('assessment_organisation.status', 'active');
            })
                ->where('id', $assessmentId)
                ->where('status', 'publish')
                ->with(['ageGroup', 'assessmentGroups.assessmentQuestions'])
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or not accessible',
                ], 404);
            }

            $assessment->total_questions_count = $assessment->assessmentGroups
                ->sum(fn($g) => $g->assessmentQuestions->count());

            return response()->json([
                'success' => true,
                'data'    => new AssessmentDetailResource($assessment),
            ]);

        } catch (\Throwable $e) {
            Log::error('getAssessmentDetail failed', [
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
                'assessment_id' => $assessmentId,
                'user_id'       => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessment details',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get attempt status for an assessment (NEW)
     * Shows: attempts used, remaining, can_attempt, current_attempt
     */
    public function getAttemptStatus(Request $request, int $assessmentId)
    {
        try {
            $user = $request->user();

            // Verify assessment exists and is accessible
            $assessment = Assessment::whereHas('organisations', function ($query) use ($user) {
                $query->where('organisations.id', $user->organisation_id)
                    ->where('assessment_organisation.status', 'active');
            })
                ->where('id', $assessmentId)
                ->where('status', 'publish')
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or not accessible',
                ], 404);
            }

            // Get max attempts
            $maxAttempts = (int) $assessment->max_attempts ?? 1;

            // Count completed attempts (submitted or evaluated)
            $completedAttempts = TestAttempt::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->whereIn('status', ['submitted', 'evaluated'])
                ->count();

            // Check for in-progress attempt
            $inProgressAttempt = TestAttempt::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->where('status', 'in_progress')
                ->first();

            // Calculate remaining attempts
            $remainingAttempts = max(0, $maxAttempts - $completedAttempts);
            $canAttempt        = $remainingAttempts > 0 && !$inProgressAttempt;

            return response()->json([
                'success' => true,
                'data'    => [
                    'can_attempt'       => $canAttempt,
                    'attempts_used'     => $completedAttempts,
                    'max_attempts'      => $maxAttempts,
                    'remaining_attempts'=> $remainingAttempts,
                    'current_attempt'   => $inProgressAttempt ? [
                        'id'        => $inProgressAttempt->id,
                        'ack_no'    => $inProgressAttempt->ack_no,
                        'status'    => $inProgressAttempt->status,
                        'started_at'=> $inProgressAttempt->started_at,
                    ] : null,
                    'message'           => !$canAttempt && !$inProgressAttempt
                        ? "You have used all {$maxAttempts} attempts. You cannot attempt this assessment anymore."
                        : null,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('getAttemptStatus failed', [
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
                'assessment_id' => $assessmentId,
                'user_id'       => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attempt status',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Initialize/Start a new test attempt
     * ✅ UPDATED: Now checks max_attempts before creating new attempt
     */
    public function startAttempt(Request $request)
    {
        try {
            $validated = $request->validate([
                'assessment_id' => 'required|integer|exists:assessments,id',
            ]);

            $user         = $request->user();
            $assessmentId = $validated['assessment_id'];

            // Check assessment accessibility
            $assessment = Assessment::whereHas('organisations', function ($query) use ($user) {
                $query->where('organisations.id', $user->organisation_id)
                    ->where('assessment_organisation.status', 'active');
            })
                ->where('id', $assessmentId)
                ->where('status', 'publish')
                ->with([
                    'assessmentGroups.assessmentQuestions.question',
                    'assessmentGroups.questionGroup',
                ])
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or not accessible',
                ], 404);
            }

            // ── NEW: Check max_attempts ──────────────────────────────
            $maxAttempts = (int) ($assessment->max_attempts ?? 1);

            // Count completed attempts
            $completedAttempts = TestAttempt::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->whereIn('status', ['submitted', 'evaluated'])
                ->count();

            // Check for existing in-progress attempt
            $existingAttempt = TestAttempt::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->where('status', 'in_progress')
                ->first();

            // If in-progress exists, resume it
            if ($existingAttempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an in-progress attempt for this assessment',
                    'data'    => new TestAttemptResource(
                        $existingAttempt->load([
                            'assessment.assessmentGroups.assessmentQuestions.question',
                            'assessment.assessmentGroups.questionGroup',
                            'responses'
                        ])
                    ),
                ], 409);
            }

            // Check if max attempts exceeded
            if ($completedAttempts >= $maxAttempts) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum attempts reached. You have completed {$completedAttempts}/{$maxAttempts} attempts.",
                    'data'    => [
                        'attempts_used' => $completedAttempts,
                        'max_attempts'  => $maxAttempts,
                    ],
                ], 403);
            }
            // ──────────────────────────────────────────────────────────

            DB::beginTransaction();

            // Create test attempt
            $testAttempt = TestAttempt::create([
                'ack_no'        => TestAttempt::generateAckNo(),
                'assessment_id' => $assessmentId,
                'user_id'       => $user->id,
                'started_at'    => now(),
                'status'        => 'in_progress',
            ]);

            // Create empty responses for all questions
            foreach ($assessment->assessmentGroups as $group) {
                foreach ($group->assessmentQuestions as $assessmentQuestion) {
                    TestAttemptResponse::create([
                        'test_attempt_id'       => $testAttempt->id,
                        'assessment_question_id'=> $assessmentQuestion->id,
                        'response'              => null,
                    ]);
                }
            }

            DB::commit();

            // Load full attempt for response
            $testAttempt->load([
                'assessment.assessmentGroups.assessmentQuestions.question',
                'assessment.assessmentGroups.questionGroup',
                'responses',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test attempt started successfully',
                'data'    => new TestAttemptResource($testAttempt),
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('startAttempt failed', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start test attempt',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get attempt details with questions
     */
    public function getAttempt(Request $request, int $attemptId)
    {
        try {
            $user = $request->user();

            $attempt = TestAttempt::where('id', $attemptId)
                ->where('user_id', $user->id)
                ->with([
                    'assessment.assessmentGroups.assessmentQuestions.question',
                    'assessment.assessmentGroups.questionGroup',
                    'assessment.ageGroup',
                    'responses',
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test attempt not found',
                ], 404);
            }

            // Check expiry
            if ($attempt->hasExpired() && $attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt has expired',
                    'expired' => true,
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data'    => new TestAttemptResource($attempt),
            ]);

        } catch (\Throwable $e) {
            Log::error('getAttempt failed', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'attempt_id' => $attemptId,
                'user_id'    => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch test attempt',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Save/Update response for a question
     */
    public function saveResponse(Request $request, int $attemptId)
    {
        try {
            $validated = $request->validate([
                'assessment_question_id' => 'required|integer|exists:assessment_questions,id',
                'response'               => 'required|array',
            ]);

            $user = $request->user();

            $attempt = TestAttempt::where('id', $attemptId)
                ->where('user_id', $user->id)
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test attempt not found',
                ], 404);
            }

            if (!$attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt is not in progress',
                ], 403);
            }

            if ($attempt->hasExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt has expired',
                    'expired' => true,
                ], 403);
            }

            $response = TestAttemptResponse::updateOrCreate(
                [
                    'test_attempt_id'        => $attemptId,
                    'assessment_question_id' => $validated['assessment_question_id'],
                ],
                [
                    'response' => $validated['response'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully',
                'data'    => new TestAttemptResponseResource($response),
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('saveResponse failed', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'attempt_id' => $attemptId,
                'user_id'    => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save response',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Submit the test attempt
     */
    public function submitAttempt(Request $request, int $attemptId)
    {
        try {
            $user = $request->user();

            $attempt = TestAttempt::where('id', $attemptId)
                ->where('user_id', $user->id)
                ->with([
                    'assessment.assessmentGroups.assessmentQuestions.question',
                    'responses',
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test attempt not found',
                ], 404);
            }

            if (!$attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt is already submitted',
                ], 403);
            }

            DB::beginTransaction();

            $totalScore    = 0;
            $negativeScore = 0;

            foreach ($attempt->responses as $response) {
                // Skip if no response
                if (empty($response->response)) {
                    continue;
                }

                $assessmentQuestion = $response->assessmentQuestion;

                if (!$assessmentQuestion || !$assessmentQuestion->question) {
                    continue;
                }

                $question        = $assessmentQuestion->question;
                $questionContent = $question->question_content ?? [];
                $userResponse    = $response->response ?? [];

                $isCorrect     = false;
                $obtainedMarks = 0;

                if ($question->answer_category === 'single_choice') {
                    $selectedOptionIndex = $userResponse['selected_option'] ?? null;

                    if ($selectedOptionIndex !== null &&
                        isset($questionContent['options'][$selectedOptionIndex])
                    ) {
                        $selectedOption = $questionContent['options'][$selectedOptionIndex];
                        $isCorrect      = (bool) ($selectedOption['is_correct'] ?? false);

                        if ($isCorrect) {
                            $obtainedMarks  = (float) ($questionContent['marks'] ?? 0);
                            $totalScore    += $obtainedMarks;
                        } elseif ($attempt->assessment->has_negative_mark) {
                            $negativeScore += (float) $assessmentQuestion->negative_mark;
                        }
                    }

                } elseif ($question->answer_category === 'multi_choice') {
                    $selectedOptions = $userResponse['selected_options'] ?? [];
                    $correctOptions  = collect($questionContent['options'] ?? [])
                        ->filter(fn($opt) => $opt['is_correct'] ?? false)
                        ->keys()
                        ->toArray();

                    $sortedSelected = $selectedOptions;
                    $sortedCorrect  = $correctOptions;
                    sort($sortedSelected);
                    sort($sortedCorrect);

                    $isCorrect = $sortedSelected === $sortedCorrect;

                    if ($isCorrect) {
                        $obtainedMarks  = (float) ($questionContent['marks'] ?? 0);
                        $totalScore    += $obtainedMarks;
                    } else {
                        // Partial marks based on weightage
                        $partialMarks = 0;
                        foreach ($selectedOptions as $optIndex) {
                            if (isset($questionContent['options'][$optIndex])) {
                                $opt = $questionContent['options'][$optIndex];
                                if ($opt['is_correct'] ?? false) {
                                    $partialMarks += (float) ($opt['weightage'] ?? 0);
                                }
                            }
                        }
                        $obtainedMarks  = $partialMarks;
                        $totalScore    += $obtainedMarks;
                    }
                }
                // open_text: not auto-evaluated

                $response->update([
                    'is_correct'     => $question->answer_category !== 'open_text'
                        ? $isCorrect
                        : null,
                    'obtained_marks' => $obtainedMarks,
                ]);
            }

            $attempt->update([
                'submitted_at'  => now(),
                'total_score'   => $totalScore,
                'negative_score'=> $negativeScore,
                'status'        => 'submitted',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Test submitted successfully',
                'data'    => new TestAttemptResource(
                    $attempt->fresh(['assessment', 'responses'])
                ),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('submitAttempt failed', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'attempt_id' => $attemptId,
                'user_id'    => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit test attempt',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get attempt result/summary
     */
    public function getAttemptResult(Request $request, int $attemptId)
    {
        try {
            $user = $request->user();

            $attempt = TestAttempt::where('id', $attemptId)
                ->where('user_id', $user->id)
                ->whereIn('status', ['submitted', 'evaluated'])
                ->with([
                    'assessment',
                    'responses.assessmentQuestion.question',
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test result not found',
                ], 404);
            }

            $totalQuestions    = $attempt->getTotalQuestionsCount();
            $answeredQuestions = $attempt->getAnsweredQuestionsCount();
            $correctAnswers    = $attempt->responses->where('is_correct', true)->count();
            $wrongAnswers      = $attempt->responses->where('is_correct', false)->count();
            $unanswered        = $attempt->responses->whereNull('response')->count();
            $finalScore        = (float) $attempt->total_score - (float) $attempt->negative_score;
            $totalMarks        = (float) $attempt->assessment->total_marks;
            $passingMarks      = (float) $attempt->assessment->passing_marks;
            $percentage        = $totalMarks > 0
                ? round(($finalScore / $totalMarks) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'data'    => [
                    'attempt' => new TestAttemptResource($attempt),
                    'summary' => [
                        'total_questions'    => $totalQuestions,
                        'answered_questions' => $answeredQuestions,
                        'correct_answers'    => $correctAnswers,
                        'wrong_answers'      => $wrongAnswers,
                        'unanswered'         => $unanswered,
                        'total_marks'        => $totalMarks,
                        'passing_marks'      => $passingMarks,
                        'obtained_marks'     => (float) $attempt->total_score,
                        'negative_marks'     => (float) $attempt->negative_score,
                        'final_score'        => $finalScore,
                        'percentage'         => $percentage,
                        'is_passed'          => $finalScore >= $passingMarks,
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('getAttemptResult failed', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'attempt_id' => $attemptId,
                'user_id'    => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attempt result',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
