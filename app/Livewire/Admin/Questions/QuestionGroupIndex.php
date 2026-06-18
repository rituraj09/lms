<?php
// app/Livewire/Admin/Questions/QuestionGroupIndex.php

namespace App\Livewire\Admin\Questions;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\QuestionMaster\QuestionGroup;

#[Layout('layouts.backend')]
class QuestionGroupIndex extends Component
{
    use WithPagination;

    // ─── Filters ──────────────────────────────────────────────────────
    public string $search        = '';
    public string $categoryFilter = '';
    public int    $perPage       = 10;

    // ─── UI State ─────────────────────────────────────────────────────
    public bool $confirmingDelete  = false;
    public ?int $deletingGroupId   = null;
    public array $expandedGroups   = [];   // track which groups show questions

    protected $queryString = [
        'search'         => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'perPage'        => ['except' => 10],
    ];

    // ─── Listeners ────────────────────────────────────────────────────
    protected $listeners = [
        'groupSaved'    => '$refresh',
        'questionSaved' => '$refresh',
    ];

    // ─── Reset pagination on filter change ───────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    // ─── Toggle question list visibility ─────────────────────────────
    public function toggleGroup(int $groupId): void
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_filter(
                $this->expandedGroups,
                fn($id) => $id !== $groupId
            );
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    // ─── Delete confirmation ───────────────────────────────────────────
    public function confirmDelete(int $groupId): void
    {
        $this->deletingGroupId  = $groupId;
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
        $this->deletingGroupId  = null;
    }

    public function deleteGroup(): void
    {
        $group = QuestionGroup::findOrFail($this->deletingGroupId);

        $this->authorize('delete', $group);

        $group->update(['updated_by' => auth()->id()]);
        $group->delete();

        $this->cancelDelete();
        session()->flash('success', 'Question group deleted successfully.');
    }

    // ─── Render ───────────────────────────────────────────────────────
    public function render()
    {
        $groups = QuestionGroup::with([
                'questions' => fn($q) => $q->withCount('assessmentQuestions'),
                'createdBy:id,name',
                'updatedBy:id,name',
            ])
            ->withCount(['questions', 'assessmentGroups'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when(
                $this->categoryFilter,
                fn($q) => $q->byCategory($this->categoryFilter)
            )
            ->latest()
            ->paginate($this->perPage);

        return view(
            'livewire.admin.questions.question-group-index',
            compact('groups')
        );
    }
}
