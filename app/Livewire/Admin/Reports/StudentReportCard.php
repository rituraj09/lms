<?php
// app/Livewire/Admin/Reports/StudentReportCard.php
namespace App\Livewire\Admin\Reports;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;
use App\Models\TestAttempt\UserPromotionDetail;

#[Layout('layouts.backend')]
class StudentReportCard extends Component
{
    public int $userId;
    public User $student;

    public array $currentPromotions = [];
    public array $promotionHistories = [];

    public string $activeHistoryType = 'iq';
    public bool $showModal = false;
    public string $backUrl = '';

    public function mount(string $id, string $organisationId = ''): void
    {
        $decryptedId = decrypt($id);
        $this->userId = $decryptedId;

        $this->backUrl = $this->resolveBackUrl($organisationId);

        $this->student = User::with([
            'details',
            'organisation',
        ])->findOrFail($decryptedId);

        $this->loadPromotionData();
    }
    private function resolveBackUrl(string $encryptedOrgId = ''): string
    {
        // If we have an organisationId in the route → org-scoped
        if (!empty($encryptedOrgId)) {
            return route('admin.org.reports.students-list', [
                'organisationId' => $encryptedOrgId,
            ]);
        }

        // Global admin route
        return route('admin.reports.reports.student-list');
    }


    public function loadPromotionData(): void
    {
        $types = ['iq', 'eq', 'lq'];

        foreach ($types as $type) {
            $current = UserPromotionDetail::with([
                'promotionDetail.currentPromotion.ageGroup',
                'promotionDetail.currentPromotion.difficultyLevel',
                'promotionDetail.nextPromotion.ageGroup',
                'promotionDetail.nextPromotion.difficultyLevel',
                'testAttempt.assessment',
            ])
                ->where('user_id', $this->userId)
                ->where('assessment_type', $type)
                ->where('current_status', true)
                ->latest()
                ->first();

            $this->currentPromotions[$type] = $current;

            $history = UserPromotionDetail::with([
                'promotionDetail.currentPromotion.ageGroup',
                'promotionDetail.currentPromotion.difficultyLevel',
                'promotionDetail.nextPromotion',
                'testAttempt.assessment',
            ])
                ->where('user_id', $this->userId)
                ->where('assessment_type', $type)
                ->latest()
                ->get();

            $this->promotionHistories[$type] = $history;
        }
    }

    public function openHistoryModal(string $type): void
    {
        $this->activeHistoryType = $type;
        $this->showModal = true;
    }

    public function setActiveHistoryType(string $type): void
    {
        $this->activeHistoryType = $type;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function getPhysicalAge(): ?string
    {
        $dob = $this->student->details?->date_of_birth;
        if (!$dob) {
            return null;
        }
        return \Carbon\Carbon::parse($dob)->age . ' years';
    }

    public function getScorePercentage(?object $attempt, ?object $assessment): float
    {
        if (!$attempt || !$assessment) {
            return 0;
        }
        $totalMarks = $assessment->total_marks ?? 0;
        if ($totalMarks <= 0) {
            return 0;
        }
        return round(($attempt->total_score / $totalMarks) * 100, 1);
    }

    public function render()
    {
        return view('livewire.admin.reports.student-report-card', [
            'physicalAge'     => $this->getPhysicalAge(),
            'assessmentTypes' => ['iq' => 'IQ', 'eq' => 'EQ', 'lq' => 'LQ'],
        ]);
    }
}
