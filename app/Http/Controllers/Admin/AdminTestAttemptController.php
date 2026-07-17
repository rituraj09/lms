<?php
// app/Http/Controllers/Admin/AdminTestAttemptController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminTestAttempt\AdminTestAttemptResource;
use App\Http\Resources\AdminTestAttempt\AdminTestAttemptResponseResource;
use App\Http\Resources\Assessment\AssessmentDetailResource;
use App\Models\AdminTestAttempt\AdminTestAttempt;
use App\Models\AdminTestAttempt\AdminTestAttemptResponse;
use App\Models\AssessmentMaster\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminTestAttemptController extends Controller
{
    /**
     * Get assessment detail for admin preview
     * Admin can access ANY assessment regardless of org/status
     */
    public function getAssessmentDetail(Request $request, int $assessmentId)
    {
        try {
            $assessment = Assessment::where('id', $assessmentId)
                ->with([
                    'ageGroup',
                    'assessmentGroups.assessmentQuestions.question',
                    'assessmentGroups.questionGroup',
                ])
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found',
                ], 404);
            }

            $assessment->total_questions_count = $assessment->assessmentGroups
                ->sum(fn($g) => $g->assessmentQuestions->count());

            return response()->json([
                'success' => true,
                'data'    => new AssessmentDetailResource($assessment),
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin getAssessmentDetail failed', [
                'error'         => $e->getMessage(),
                'assessment_id' => $assessmentId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessment details',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Start an admin preview attempt
     */
    public function startAttempt(Request $request)
    {
        try {
            $validated = $request->validate([
                'assessment_id' => 'required|integer|exists:assessments,id',
            ]);

            $admin        = $request->user(); // Admin model
            $assessmentId = $validated['assessment_id'];

            // Load assessment with questions
            $assessment = Assessment::where('id', $assessmentId)
                ->with([
                    'assessmentGroups.assessmentQuestions.question',
                    'assessmentGroups.questionGroup',
                ])
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found',
                ], 404);
            }

            // Check for existing in-progress admin preview
            $existingAttempt = AdminTestAttempt::where('admin_id', $admin->id)
                ->where('assessment_id', $assessmentId)
                ->where('status', 'in_progress')
                ->first();

            if ($existingAttempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an in-progress preview for this assessment',
                    'data'    => new AdminTestAttemptResource(
                        $existingAttempt->load([
                            'assessment.assessmentGroups.assessmentQuestions.question',
                            'assessment.assessmentGroups.questionGroup',
                            'responses',
                        ])
                    ),
                ], 409);
            }

            DB::beginTransaction();

            // Create admin test attempt
            $adminAttempt = AdminTestAttempt::create([
                'ack_no'        => AdminTestAttempt::generateAckNo(),
                'assessment_id' => $assessmentId,
                'admin_id'      => $admin->id,
                'started_at'    => now(),
                'status'        => 'in_progress',
            ]);

            // Create empty responses for all questions
            foreach ($assessment->assessmentGroups as $group) {
                foreach ($group->assessmentQuestions as $assessmentQuestion) {
                    AdminTestAttemptResponse::create([
                        'admin_test_attempt_id'  => $adminAttempt->id,
                        'assessment_question_id' => $assessmentQuestion->id,
                        'response'               => null,
                    ]);
                }
            }

            DB::commit();

            // Load full attempt for response
            $adminAttempt->load([
                'assessment.assessmentGroups.assessmentQuestions.question',
                'assessment.assessmentGroups.questionGroup',
                'responses',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin preview started successfully',
                'data'    => new AdminTestAttemptResource($adminAttempt),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin startAttempt failed', [
                'error'    => $e->getMessage(),
                'admin_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start admin preview',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get admin attempt details with questions
     */
    public function getAttempt(Request $request, int $attemptId)
    {
        try {
            $admin = $request->user();

            $attempt = AdminTestAttempt::where('id', $attemptId)
                ->where('admin_id', $admin->id)
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
                    'message' => 'Admin preview attempt not found',
                ], 404);
            }

            // Check expiry
            if ($attempt->hasExpired() && $attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This preview attempt has expired',
                    'expired' => true,
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data'    => new AdminTestAttemptResource($attempt),
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin getAttempt failed', [
                'error'      => $e->getMessage(),
                'attempt_id' => $attemptId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin preview attempt',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Save response for a question during admin preview
     */
    public function saveResponse(Request $request, int $attemptId)
    {
        try {
            $validated = $request->validate([
                'assessment_question_id' => 'required|integer|exists:assessment_questions,id',
                'response'               => 'required|array',
            ]);

            $admin = $request->user();

            $attempt = AdminTestAttempt::where('id', $attemptId)
                ->where('admin_id', $admin->id)
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin preview attempt not found',
                ], 404);
            }

            if (!$attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This preview attempt is not in progress',
                ], 403);
            }

            if ($attempt->hasExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This preview attempt has expired',
                    'expired' => true,
                ], 403);
            }

            $response = AdminTestAttemptResponse::updateOrCreate(
                [
                    'admin_test_attempt_id'  => $attemptId,
                    'assessment_question_id' => $validated['assessment_question_id'],
                ],
                [
                    'response' => $validated['response'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully',
                'data'    => new AdminTestAttemptResponseResource($response),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Admin saveResponse failed', [
                'error'      => $e->getMessage(),
                'attempt_id' => $attemptId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save response',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Submit/End admin preview attempt
     */
    public function submitAttempt(Request $request, int $attemptId)
    {
        try {
            $admin = $request->user();

            $attempt = AdminTestAttempt::where('id', $attemptId)
                ->where('admin_id', $admin->id)
                ->with([
                    'assessment.assessmentGroups.assessmentQuestions.question',
                    'responses',
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin preview attempt not found',
                ], 404);
            }

            if (!$attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This preview attempt is already submitted',
                ], 403);
            }

            DB::beginTransaction();

            $totalScore    = 0;
            $negativeScore = 0;
            $hasOpenText   = false;

            foreach ($attempt->responses as $response) {
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

                $isCorrect    = false;
                $obtainedMarks = 0;

                if ($question->answer_category === 'single_choice') {
                    $selectedOptionIndex = $userResponse['selected_option'] ?? null;
                    if (
                        $selectedOptionIndex !== null &&
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
                } elseif ($question->answer_category === 'open_text') {
                    $hasOpenText = true;
                }

                $response->update([
                    'is_correct'    => $question->answer_category !== 'open_text'
                        ? $isCorrect
                        : null,
                    'obtained_marks'=> $obtainedMarks,
                ]);
            }

            $status = $hasOpenText ? 'submitted' : 'evaluated';

            $attempt->update([
                'submitted_at'  => now(),
                'total_score'   => $totalScore,
                'negative_score'=> $negativeScore,
                'status'        => $status,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin preview submitted successfully',
                'data'    => new AdminTestAttemptResource(
                    $attempt->fresh(['assessment', 'responses'])
                ),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin submitAttempt failed', [
                'error'      => $e->getMessage(),
                'attempt_id' => $attemptId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit admin preview',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Delete/Reset admin preview attempts for an assessment
     * Useful so admin can preview fresh
     */
    public function resetPreview(Request $request, int $assessmentId)
    {
        try {
            $admin = $request->user();

            AdminTestAttempt::where('admin_id', $admin->id)
                ->where('assessment_id', $assessmentId)
                ->each(function ($attempt) {
                    $attempt->responses()->delete();
                    $attempt->delete();
                });

            return response()->json([
                'success' => true,
                'message' => 'Preview reset successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin resetPreview failed', [
                'error'         => $e->getMessage(),
                'assessment_id' => $assessmentId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset preview',
            ], 500);
        }
    }

    /**
     * Get admin preview result/summary
     */
    public function getAttemptResult(Request $request, int $attemptId)
    {
        try {
            $admin = $request->user();

            $attempt = AdminTestAttempt::where('id', $attemptId)
                ->where('admin_id', $admin->id)
                ->whereIn('status', ['submitted', 'evaluated'])
                ->with([
                    'assessment',
                    'responses.assessmentQuestion.question',
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin preview result not found',
                ], 404);
            }

            $totalQuestions    = $attempt->getTotalQuestionsCount();
            $answeredQuestions = $attempt->getAnsweredQuestionsCount();
            $correctAnswers    = $attempt->responses->where('is_correct', true)->count();
            $wrongAnswers      = $attempt->responses->where('is_correct', false)->count();
            $unanswered        = $attempt->responses->whereNull('response')->count();

            $finalScore   = (float) $attempt->total_score - (float) $attempt->negative_score;
            $totalMarks   = (float) $attempt->assessment->total_marks;
            $passingMarks = (float) $attempt->assessment->passing_marks;

            $percentage = $totalMarks > 0
                ? round(($finalScore / $totalMarks) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'data'    => [
                    'attempt' => new AdminTestAttemptResource($attempt),
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
            Log::error('Admin getAttemptResult failed', [
                'error'      => $e->getMessage(),
                'attempt_id' => $attemptId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin preview result',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
