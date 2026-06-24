<?php
// app/Http/Controllers/Api/TestAttemptController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestAttempt\TestAttemptResource;
use App\Http\Resources\TestAttempt\TestAttemptListResource;
use App\Http\Resources\Assessment\AssessmentDetailResource;
use App\Models\TestAttempt\TestAttempt\TestAttempt;
use App\Models\TestAttempt\TestAttempt\TestAttemptResponse;
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
                ->with(['ageGroup', 'assessmentGroups'])
                ->withCount([
                    'assessmentGroups as total_questions' => function ($q) {
                        $q->join('assessment_questions', 'assessment_groups.id', '=', 'assessment_questions.assessment_group_id');
                    }
                ])
                ->get();

            return response()->json([
                'success' => true,
                'data' => AssessmentDetailResource::collection($assessments)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching assigned assessments', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessments'
            ], 500);
        }
    }

    /**
     * Get user's test attempts
     */
    public function myAttempts(Request $request)
    {
        try {
            $user = $request->user();

            $attempts = TestAttempt::where('user_id', $user->id)
                ->with(['assessment'])
                ->latest()
                ->paginate(10);

            return TestAttemptListResource::collection($attempts);
        } catch (\Exception $e) {
            Log::error('Error fetching user attempts', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attempts'
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
                ->with(['ageGroup'])
                ->withCount([
                    'assessmentGroups as total_questions' => function ($q) {
                        $q->join('assessment_questions', 'assessment_groups.id', '=', 'assessment_questions.assessment_group_id');
                    }
                ])
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or not accessible'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new AssessmentDetailResource($assessment)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching assessment detail', [
                'error' => $e->getMessage(),
                'assessment_id' => $assessmentId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessment details'
            ], 500);
        }
    }

    /**
     * Initialize/Start a new test attempt
     */
    public function startAttempt(Request $request)
    {
        try {
            $validated = $request->validate([
                'assessment_id' => 'required|integer|exists:assessments,id',
            ]);

            $user = $request->user();
            $assessmentId = $validated['assessment_id'];

            // Check if assessment is accessible
            $assessment = Assessment::whereHas('organisations', function ($query) use ($user) {
                $query->where('organisations.id', $user->organisation_id)
                    ->where('assessment_organisation.status', 'active');
            })
                ->where('id', $assessmentId)
                ->where('status', 'publish')
                ->with([
                    'assessmentGroups.assessmentQuestions.question',
                    'assessmentGroups.questionGroup'
                ])
                ->first();

            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or not accessible'
                ], 404);
            }

            // Check if user already has an in-progress attempt
            $existingAttempt = TestAttempt::where('user_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->where('status', 'in_progress')
                ->first();

            if ($existingAttempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an in-progress attempt for this assessment',
                    'data' => new TestAttemptResource($existingAttempt->load(['assessment', 'responses']))
                ], 409);
            }

            DB::beginTransaction();

            // Create test attempt
            $testAttempt = TestAttempt::create([
                'ack_no' => TestAttempt::generateAckNo(),
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'started_at' => now(),
                'status' => 'in_progress',
            ]);

            // Create empty responses for all questions
            foreach ($assessment->assessmentGroups as $group) {
                foreach ($group->assessmentQuestions as $assessmentQuestion) {
                    TestAttemptResponse::create([
                        'test_attempt_id' => $testAttempt->id,
                        'assessment_question_id' => $assessmentQuestion->id,
                        'response' => null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Test attempt started successfully',
                'data' => new TestAttemptResource($testAttempt->load(['assessment', 'responses']))
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error starting test attempt', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start test attempt'
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
                    'responses'
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test attempt not found'
                ], 404);
            }

            // Check if attempt has expired
            if ($attempt->hasExpired() && $attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt has expired',
                    'expired' => true
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => new TestAttemptResource($attempt)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching test attempt', [
                'error' => $e->getMessage(),
                'attempt_id' => $attemptId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch test attempt'
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
                'response' => 'required|array',
            ]);

            $user = $request->user();

            $attempt = TestAttempt::where('id', $attemptId)
                ->where('user_id', $user->id)
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test attempt not found'
                ], 404);
            }

            if (!$attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt is not in progress'
                ], 403);
            }

            // Check if attempt has expired
            if ($attempt->hasExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt has expired',
                    'expired' => true
                ], 403);
            }

            // Update or create response
            $response = TestAttemptResponse::updateOrCreate(
                [
                    'test_attempt_id' => $attemptId,
                    'assessment_question_id' => $validated['assessment_question_id'],
                ],
                [
                    'response' => $validated['response'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully',
                'data' => new TestAttemptResponseResource($response)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saving response', [
                'error' => $e->getMessage(),
                'attempt_id' => $attemptId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save response'
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
                    'responses'
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test attempt not found'
                ], 404);
            }

            if (!$attempt->isInProgress()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attempt is already submitted'
                ], 403);
            }

            DB::beginTransaction();

            // Evaluate responses and calculate scores
            $totalScore = 0;
            $negativeScore = 0;

            foreach ($attempt->responses as $response) {
                $assessmentQuestion = $response->assessmentQuestion;
                $question = $assessmentQuestion->question;
                $questionContent = $question->question_content ?? [];
                $userResponse = $response->response ?? [];

                // Skip if no response
                if (empty($userResponse)) {
                    continue;
                }

                // Evaluate based on answer category
                $isCorrect = false;
                $obtainedMarks = 0;

                if ($question->answer_category === 'single_choice') {
                    // Single choice evaluation
                    $selectedOptionIndex = $userResponse['selected_option'] ?? null;

                    if ($selectedOptionIndex !== null && isset($questionContent['options'][$selectedOptionIndex])) {
                        $selectedOption = $questionContent['options'][$selectedOptionIndex];
                        $isCorrect = $selectedOption['is_correct'] ?? false;

                        if ($isCorrect) {
                            $obtainedMarks = $questionContent['marks'] ?? 0;
                            $totalScore += $obtainedMarks;
                        } else {
                            // Apply negative marking
                            if ($attempt->assessment->has_negative_mark) {
                                $negativeScore += $assessmentQuestion->negative_mark;
                            }
                        }
                    }
                } elseif ($question->answer_category === 'multi_choice') {
                    // Multi choice evaluation
                    $selectedOptions = $userResponse['selected_options'] ?? [];
                    $correctOptions = collect($questionContent['options'] ?? [])
                        ->filter(fn($opt) => $opt['is_correct'] ?? false)
                        ->keys()
                        ->toArray();

                    sort($selectedOptions);
                    sort($correctOptions);

                    $isCorrect = $selectedOptions === $correctOptions;

                    if ($isCorrect) {
                        $obtainedMarks = $questionContent['marks'] ?? 0;
                        $totalScore += $obtainedMarks;
                    } else {
                        // Partial marking based on weightage (optional)
                        $partialMarks = 0;
                        foreach ($selectedOptions as $optIndex) {
                            if (isset($questionContent['options'][$optIndex])) {
                                $option = $questionContent['options'][$optIndex];
                                if ($option['is_correct'] ?? false) {
                                    $partialMarks += ($option['weightage'] ?? 0);
                                }
                            }
                        }
                        $obtainedMarks = $partialMarks;
                        $totalScore += $obtainedMarks;
                    }
                }
                // open_text questions are not auto-evaluated

                // Update response
                $response->update([
                    'is_correct' => $isCorrect,
                    'obtained_marks' => $obtainedMarks,
                ]);
            }

            // Update attempt
            $attempt->update([
                'submitted_at' => now(),
                'total_score' => $totalScore,
                'negative_score' => $negativeScore,
                'status' => 'submitted',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Test submitted successfully',
                'data' => new TestAttemptResource($attempt->fresh(['assessment', 'responses']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting test attempt', [
                'error' => $e->getMessage(),
                'attempt_id' => $attemptId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit test attempt'
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
                    'responses.assessmentQuestion.question'
                ])
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test result not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'attempt' => new TestAttemptResource($attempt),
                    'summary' => [
                        'total_questions' => $attempt->getTotalQuestionsCount(),
                        'answered_questions' => $attempt->getAnsweredQuestionsCount(),
                        'correct_answers' => $attempt->responses()->where('is_correct', true)->count(),
                        'wrong_answers' => $attempt->responses()->where('is_correct', false)->count(),
                        'unanswered' => $attempt->responses()->whereNull('response')->count(),
                        'total_marks' => (float) $attempt->assessment->total_marks,
                        'passing_marks' => (float) $attempt->assessment->passing_marks,
                        'obtained_marks' => (float) $attempt->total_score,
                        'negative_marks' => (float) $attempt->negative_score,
                        'final_score' => (float) ($attempt->total_score - $attempt->negative_score),
                        'percentage' => $attempt->assessment->total_marks > 0
                            ? round((($attempt->total_score - $attempt->negative_score) / $attempt->assessment->total_marks) * 100, 2)
                            : 0,
                        'is_passed' => ($attempt->total_score - $attempt->negative_score) >= $attempt->assessment->passing_marks,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching attempt result', [
                'error' => $e->getMessage(),
                'attempt_id' => $attemptId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attempt result'
            ], 500);
        }
    }
}
