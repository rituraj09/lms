<?php

namespace App\Livewire\Admin\AssessmentMasters;

use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AssessmentMaster\Assessment;

#[Layout('layouts.backend')]
class AssessmentList extends Component
{
    public string $search       = '';
    public string $statusFilter = '';
    public string $typeFilter   = '';
    public int    $perPage      = 10;

    public array $assessmentTypes = [
        'iq'       => 'IQ',
        'eq'       => 'EQ',
        'lq'       => 'LQ',
        'iq+eq'    => 'IQ + EQ',
        'iq+lq'    => 'IQ + LQ',
        'eq+lq'    => 'EQ + LQ',
        'iq+eq+lq' => 'IQ + EQ + LQ',
    ];

    /* ================================================================
     |  COMPUTED — Assessment List
     * ================================================================*/
    public function getAssessmentsProperty()
    {
        return Assessment::with(['ageGroup', 'createdBy'])
            ->withCount([
                'assessmentQuestions',
                'testAttempts as attempts_count',
                'testAttempts as in_progress_count' => fn($q) =>
                $q->where('status', 'in_progress'),
                'testAttempts as completed_count' => fn($q) =>
                $q->whereIn('status', ['submitted', 'evaluated']),
            ])
            ->when($this->search, fn($q) =>
            $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('assessment_code', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn($q) =>
            $q->where('status', $this->statusFilter)
            )
            ->when($this->typeFilter, fn($q) =>
            $q->where('assessment_type_id', $this->typeFilter)
            )
            ->latest()
            ->paginate($this->perPage);
    }

    /* ================================================================
     |  LIST ACTIONS
     * ================================================================*/
    public function createAssessment(): void
    {
        $this->redirect(route('admin.assessment-masters.manage'), navigate: false);
    }

    public function editAssessment(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        if ($assessment->isLockedForEditing()) {
            session()->flash(
                'error',
                'This assessment cannot be edited because students have already attempted it. You can only change its status (Publish/Unpublish).'
            );
            return;
        }

        $this->redirect(
            route('assessment-masters.manage', ['assessmentId' => encrypt($id)]),
            navigate: false
        );
    }

    public function openBuilder(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        if ($assessment->isLockedForEditing()) {
            session()->flash(
                'error',
                'This assessment is locked for editing because students have already attempted it.'
            );
            return;
        }

        $this->redirect(
            route('assessment-masters.build', ['assessmentId' => encrypt($id)]),
            navigate: false
        );
    }

    public function deleteAssessment(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        if ($assessment->hasAttempts()) {
            session()->flash(
                'error',
                'Cannot delete this assessment because students have already attempted it.'
            );
            return;
        }

        if ($assessment->status === 'publish') {
            session()->flash('error', 'Cannot delete a published assessment.');
            return;
        }

        $assessment->delete();
        session()->flash('success', 'Assessment deleted successfully.');
    }

    /* ================================================================
     |  CHANGE STATUS
     * ================================================================*/
    public function changeStatus(int $id, string $newStatus): void
    {
        if (! in_array($newStatus, ['draft', 'publish', 'unpublish'])) {
            session()->flash('error', 'Invalid status value.');
            return;
        }

        $assessment = Assessment::findOrFail($id);

        if ($assessment->hasAttempts() && $newStatus === 'draft') {
            session()->flash(
                'error',
                'Cannot set to Draft because students have already attempted this assessment. Only Publish or Unpublish is allowed.'
            );
            return;
        }

        if ($newStatus === 'publish') {
            $hasQuestions = $assessment->assessmentGroups()
                ->whereHas('assessmentQuestions')
                ->exists();

            if (! $hasQuestions) {
                session()->flash('error', 'Cannot publish an assessment with no questions.');
                return;
            }
        }
        ActivityLogger::log(
            userId:   auth('admin')->id(),
            userType: 'admin',
            action:   'change',
            extra: [
                'model_type'  => 'Assessment',
                'model_id'    => $id,
                'description' => "Change status of the assessment to $newStatus. Assessment code:  $assessment->assessment_code",

            ]
        );
        $assessment->status     = $newStatus;
        $assessment->updated_by = auth()->id();
        $assessment->save();

        session()->flash('success', 'Assessment status changed to ' . ucfirst($newStatus) . '.');
    }

    public function toggleStatus(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        $newStatus = $assessment->status === 'publish'
            ? 'unpublish'
            : 'publish';

        $this->changeStatus($id, $newStatus);
    }

    /* ================================================================
     |  RENDER
     * ================================================================*/
    public function render()
    {
        return view('livewire.admin.assessment-masters.assessment-list', [
            'assessments' => $this->assessments,
        ]);
    }
}
