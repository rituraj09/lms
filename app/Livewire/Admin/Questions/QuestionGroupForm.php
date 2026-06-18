<?php
// app/Livewire/Admin/Questions/QuestionGroupForm.php

namespace App\Livewire\Admin\Questions;

use Livewire\Component;
use App\Models\QuestionMaster\QuestionGroup;
use Illuminate\Support\Str;

#[Layout('layouts.backend')]
class QuestionGroupForm extends Component
{
    // ─── Props ────────────────────────────────────────────────────────
    public ?int $groupId = null;

    // ─── Form Fields ──────────────────────────────────────────────────
    public string $group_code         = '';
    public string $questions_category = 'single';
    public string $title              = '';
    public string $admin_note         = '';

    // group_content: flexible JSON structure stored as array
    public array $group_content = [];

    // ─── State ────────────────────────────────────────────────────────
    public bool        $isLocked      = false;  // locked if in assessment
    public ?QuestionGroup $group      = null;

    // ─── Child question form visibility ───────────────────────────────
    public bool $showQuestionForm    = false;
    public ?int $editingQuestionId   = null;

    // ─── Validation Rules ─────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'group_code'         => 'required|string|max:100|unique:question_groups,group_code,'
                                    . ($this->groupId ?? 'NULL'),
            'questions_category' => 'required|in:single,multiple',
            'title'              => 'nullable|string|max:500',
            'admin_note'         => 'nullable|string|max:500',
            'group_content'      => 'nullable|array',
        ];
    }

    // ─── Mount ────────────────────────────────────────────────────────
    public function mount(?int $groupId = null): void
    {
        $this->groupId = $groupId;

        if ($groupId) {
            $this->group    = QuestionGroup::with('questions')->findOrFail($groupId);
            $this->isLocked = $this->group->isLinkedToAssessment();
            $this->fillForm($this->group);
        } else {
            $this->group_code = $this->generateGroupCode();
        }
    }

    private function fillForm(QuestionGroup $group): void
    {
        $this->group_code         = $group->group_code;
        $this->questions_category = $group->questions_category;
        $this->title              = $group->title;
        $this->admin_note         = $group->admin_note ?? '';
        $this->group_content      = $group->group_content ?? [];
    }

    private function generateGroupCode(): string
    {
        return 'GRP-' . strtoupper(Str::random(8));
    }

    // ─── Save Group ───────────────────────────────────────────────────
    public function saveGroup(): void
    {
        if ($this->isLocked) {
            $this->addError('locked', 'This group is linked to an assessment and cannot be edited.');
            return;
        }

        $validated = $this->validate();

        $data = array_merge($validated, [
            'updated_by' => auth()->id(),
        ]);

        if (! $this->groupId) {
            $data['created_by'] = auth()->id();
            $this->group        = QuestionGroup::create($data);
            $this->groupId      = $this->group->id;
            session()->flash('success', 'Question group created successfully.');
        } else {
            $this->group->update($data);
            session()->flash('success', 'Question group updated successfully.');
        }

        $this->group->refresh();
        $this->dispatch('groupSaved');
    }

    // ─── Question Form Controls ───────────────────────────────────────
    public function addNewQuestion(): void
    {
        if (! $this->groupId) {
            // Auto-save group first so question has a parent
            $this->saveGroup();
        }
        $this->editingQuestionId = null;
        $this->showQuestionForm  = true;
    }

    public function editQuestion(int $questionId): void
    {
        $this->editingQuestionId = $questionId;
        $this->showQuestionForm  = true;
    }

    public function closeQuestionForm(): void
    {
        $this->showQuestionForm  = false;
        $this->editingQuestionId = null;
        // Refresh group with latest questions
        if ($this->groupId) {
            $this->group = QuestionGroup::with('questions')->find($this->groupId);
        }
    }

    // ─── Render ───────────────────────────────────────────────────────
    public function render()
    {
        $questions = $this->groupId
            ? QuestionGroup::with([
                'questions.primarySkill',
                'questions.subSkill',
                'questions.difficultyLevel',
              ])
              ->find($this->groupId)
              ?->questions ?? collect()
            : collect();

        return view(
            'livewire.admin.questions.question-group-form',
            compact('questions')
        );
    }
}
