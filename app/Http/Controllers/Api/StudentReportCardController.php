<?php

// app/Http/Controllers/Api/StudentReportCardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentReportResource;
use App\Http\Resources\UserPromotionDetailResource;
use App\Http\Resources\TestAttempt\TestAttemptResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentReportCardController extends Controller
{
    /**
     * Get authenticated student's report card
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Load user with necessary relations
        $user->load([
            'details',
            'organisation',
        ]);

        // Get current promotions for each assessment type
        $currentPromotions = [];

        foreach (['iq', 'eq', 'lq'] as $type) {
            $promotion = $user->userPromotions()
                ->where('assessment_type', $type)
                ->where('current_status', true)
                ->with([
                    'promotionDetail.currentPromotion.ageGroup',
                    'promotionDetail.currentPromotion.difficultyLevel',
                    'promotionDetail.nextPromotion',
                    'testAttempt.assessment'
                ])
                ->latest()
                ->first();

            $currentPromotions[$type] = $promotion
                ? new UserPromotionDetailResource($promotion)
                : null;
        }

        // Create resource with current promotions
        $resource = new StudentReportResource($user);
        $resource->current_promotions = $currentPromotions;

        return response()->json([
            'success' => true,
            'data' => $resource,
        ]);
    }

    /**
     * Get promotion history for a specific assessment type
     */
    public function promotionHistory(Request $request, $type)
    {
        $request->validate([
            'type' => 'in:iq,eq,lq'
        ]);

        $user = $request->user();

        $history = $user->userPromotions()
            ->where('assessment_type', $type)
            ->with([
                'promotionDetail.currentPromotion.ageGroup',
                'promotionDetail.currentPromotion.difficultyLevel',
                'promotionDetail.nextPromotion',
                'testAttempt.assessment'
            ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => UserPromotionDetailResource::collection($history),
        ]);
    }

    /**
     * Get all test attempts for authenticated student
     */
    public function testAttempts(Request $request)
    {
        $user = $request->user();

        $attempts = $user->testAttempts()
            ->with([
        'assessment',
        'assessment.ageGroup',           // ✅ Add this
        'assessment.difficultyLevel',    // ✅ Add this
    ])
            ->where('status', 'evaluated')
            ->latest('submitted_at')
            ->paginate(10);

        return TestAttemptResource::collection($attempts);
    }

    /**
     * Get specific test attempt details
     */
    public function testAttemptDetail(Request $request, $id)
    {
        $user = $request->user();

        $attempt = $user->testAttempts()
            ->with(['assessment', 'responses.assessmentQuestion'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new TestAttemptResource($attempt),
        ]);
    }
}
