<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Master\Organisation;
use Illuminate\Support\Facades\Crypt;

#[Layout('layouts.backend')]
class StudentList extends Component
{
    public int $view = 0; // 0 = datatable, 1 = user details
    public ?User $selectedUser = null;

    public function mount()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
    }

    #[On('edit')]
    public function viewUserDetails(int $id): void
    {
        $this->selectedUser = User::with([
            'details',
            'organisation',
            'userPromotions' => function($query) {
                $query->current();
            },
            'userPromotions.promotionDetail.currentPromotion',
        ])->findOrFail($id);

        // Build promotion map
        $this->selectedUser->promotionMap = [
            'iq' => $this->getPromotionName($this->selectedUser, 'iq'),
            'eq' => $this->getPromotionName($this->selectedUser, 'eq'),
            'lq' => $this->getPromotionName($this->selectedUser, 'lq'),
        ];

        $this->view = 1;
    }

    #[On('report_card')]
    public function viewUserReport(int $id): void
    {
        $this->redirect(
            route('admin.reports.detailed-report', ['studentId' => $id]),
            navigate: false
        );
    }

    public function backToList(): void
    {
        $this->view = 0;
        $this->selectedUser = null;
    }

    /**
     * Get the promotion name for a specific assessment type
     */
    private function getPromotionName($student, string $type): ?string
    {
        $userPromotion = collect($student->userPromotions ?? [])
            ->where('assessment_type', $type)
            ->first();

        if (!$userPromotion) {
            return null;
        }

        return $userPromotion->promotionDetail?->currentPromotion?->name;
    }

    public function render()
    {
        return view('livewire.admin.reports.student-list');
    }
}
