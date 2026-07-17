<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Assessment\AssessmentBasicResource;
use App\Http\Resources\Assessment\AssessmentDetailResource;
use App\Models\Admin;
use App\Models\AssessmentMaster\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Assessment\AdminAssessmentResource;

class AdminAssessmentController extends Controller
{
    /**
     * Ensure only super_admin or admins with assessment view
     * permission can access this controller.
     */
    private function authorizeAssessmentAccess(Request $request): Admin
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            abort(403, 'Admin authentication required.');
        }

        $allowed = $admin->isSuperAdmin()
            || $admin->hasSystemPermission('system.assessment.view')
            || $admin->hasSystemPermission('system.assessment.create');

        if (!$allowed) {
            abort(403, 'You do not have permission to access assessments.');
        }

        return $admin;
    }

    /**
     * Get ALL assessments for admin panel.
     * Includes publish, unpublish, and draft statuses.
     * No organisation filtering — admin sees everything.
     */
    public function index(Request $request)
    {
        try {
            $admin = $this->authorizeAssessmentAccess($request);

            $query = Assessment::with([
                'assessmentGroups.assessmentQuestions',
                'ageGroup',         // ✅ needed for age_group
                'difficultyLevel',  // ✅ needed for difficulty_level & difficulty_name
            ]);

            // Optional filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('assessment_type_id')) {
                $query->where('assessment_type_id', $request->assessment_type_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('assessment_code', 'like', "%{$search}%");
                });
            }

            $assessments = $query->latest()->get();

            $assessments->each(function ($assessment) {
                $assessment->total_questions_count = $assessment->assessmentGroups
                    ->sum(fn($g) => $g->assessmentQuestions->count());
            });

            return response()->json([
                'success' => true,
                'data'    => AdminAssessmentResource::collection($assessments), // ✅ Fixed
                'meta'    => [
                    'total'       => $assessments->count(),
                    'published'   => $assessments->where('status', 'publish')->count(),
                    'unpublished' => $assessments->where('status', 'unpublish')->count(),
                    'draft'       => $assessments->where('status', 'draft')->count(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Admin getAllAssessments failed', [
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
                'admin_id' => optional($request->user())->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessments',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get assessment detail with full questions for admin preview.
     * No status restriction — draft/unpublish/publish all allowed.
     * No TestAttempt is created here. Preview only.
     */
    public function getAssessmentDetail(Request $request, int $assessmentId)
    {
        try {


            $assessment = Assessment::where('id', $assessmentId)
                ->with(['ageGroup', 'assessmentGroups.assessmentQuestions'])
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
                'trace'         => $e->getTraceAsString(),
                'assessment_id' => $assessmentId,
                'admin_id'      => optional($request->user())->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assessment details',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
