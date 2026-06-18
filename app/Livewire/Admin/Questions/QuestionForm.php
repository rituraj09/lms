<?php
// app/Livewire/Admin/Questions/QuestionForm.php

namespace App\Livewire\Admin\Questions;

use Livewire\Component;
use App\Models\QuestionMaster\Question;
use App\Models\QuestionMaster\QuestionGroup;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\SubSkillType;
use App\Models\EvaluationMaster\DifficultyLevel;
use App\Models\EvaluationMaster\AgeGroup;
use Illuminate\Support\Str;

#[Layout('layouts.backend')]
class QuestionForm extends Component
{
    // ─── Props ────────────────────────────────────────────────────────
    public int  $questionGroupId;
    public ?int $questionId = null;

    // ─── Form Fields ──────────────────────────────────────────────────
    public string $question_code     = '';
    public ?int   $primary_skill_id  = null;
    public ?int   $sub_skill_id      = null;
    public ?int   $difficulty_level_id = null;
    public ?int   $age_group_id      = null;
    public string $answer_category   = 'optional';
    public string $explaination      = '';
    public string $admin_notes       = '';

    // question_content: dynamic options builder
    public array  $question_content  = [
        'question_text' => '',
        'options'       => [],
        'correct_answer'=> null,
    ];

    // ─── State ────────────────────────────────────────────────────────
    public bool    $isLocked  = false;
    public ?Question $question = null;
    public bool    $saveAndNew = false;

    // ─── Reference Data ───────────────────────────────────────────────
    public array $primarySkills    = [];
    public array $subSkills        = [];
    public array $difficultyLevels = [];
    public array $ageGroups        = [];

    // ─── Validation ───────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'question_code'       => 'required|string|max:100|unique:questions,question_code,'
                                     . ($this->questionId ?? 'NULL'),
            'primary_skill_id'    => 'required|exists:primary_skill_types,id',
            'sub_skill_id'        => 'required|exists:sub_skill_types,id',
            'difficulty_level_id' => 'required|exists:difficulty_levels,id',
            'age_group_id'        => 'required|exists:age_groups,id',
            'answer_category'     => 'required|in:optional,open_text',
            'explaination'        => 'nullable|string',
            'admin_notes'         => 'nullable|string|max:500',
            'question_content'    => 'required|array',
            'question_content.question_text' => 'required|string',
            'question_content.options'       => 'nullable|array',
        ];
    }

    // ─── Mount ────────────────────────────────────────────────────────
    public function mount(int $questionGroupId, ?int $questionId = null): void
    {
        $this->questionGroupId = $questionGroupId;
        $this->questionId      = $questionId;

        // Load reference data
        $this->primarySkills    = PrimarySkillType::orderBy('name')->get(['id', 'name'])->toArray();
        $this->subSkills        = SubSkillType::orderBy('name')->get(['id', 'name'])->toArray();
        $this->difficultyLevels = DifficultyLevel::orderBy('name')->get(['id', 'name'])->toArray();
        $this->ageGroups        = AgeGroup::orderBy('name')->get(['id', 'name'])->toArray();

        if ($questionId) {
            $this->question  = Question::findOrFail($questionId);
            $this->isLocked  = $this->question->isUsedInAssessment();
            $this->fillForm($this->question);
        } else {
            $this->question_code = $this->generateQuestionCode();
        }
    }

    private function fillForm(Question $question): void
    {
        $this->question_code      = $question->question_code;
        $this->primary_skill_id   = $question->primary_skill_id;
        $this->sub_skill_id       = $question->sub_skill_id;
        $this->difficulty_level_id= $question->difficulty_level_id;
        $this->age_group_id       = $question->age_group_id;
        $this->answer_category    = $question->answer_category;
        $this->explaination       = $question->explaination ?? '';
        $this->admin_notes        = $question->admin_notes ?? '';
        $this->question_content   = $question->question_content ?? [
            'question_text' => '',
            'options'       => [],
            'correct_answer'=> null,
        ];
    }

    private function generateQuestionCode(): string
    {
        return 'QST-' . strtoupper(Str::random(8));
    }

    // ─── Dynamic Options Builder ──────────────────────────────────────
    public function addOption(): void
    {
        $this->question_content['options'][] = [
            'id'    => Str::uuid(),
            'text'  => '',
            'is_correct' => false,
        ];
    }

    public function removeOption(int $index): void
    {
        unset($this->question_content['options'][$index]);
        $this->question_content['options'] = array_values(
            $this->question_content['options']
        );
    }

    public function setCorrectOption(int $index): void
    {
        // For single-answer groups, only one correct
        $group = QuestionGroup::find($this->questionGroupId);

        foreach ($this->question_content['options'] as $i => $option) {
            $this->question_content['options'][$i]['is_correct'] =
                ($group?->questions_category === 'single')
                    ? ($i === $index)
                    : ($i === $index
                        ? ! $option['is_correct']
                        : $option['is_correct']);
        }
    }

    // ─── Save Question ────────────────────────────────────────────────
    public function saveQuestion(bool $andNew = false): void
    {
        if ($this->isLocked) {
            $this->addError('locked', 'This question is used in an assessment and cannot be edited.');
            return;
        }

        $validated = $this->validate();

        $data = array_merge($validated, [
            'question_group_id' => $this->questionGroupId,
            'updated_by'        => auth()->id(),
        ]);

        if (! $this->questionId) {
            $data['created_by'] = auth()->id();
            $this->question     = Question::create($data);
            session()->flash('success', 'Question saved successfully.');
        } else {
            $this->question->update($data);
            session()->flash('success', 'Question updated successfully.');
        }

        $this->dispatch('questionSaved', questionId: $this->question->id);

        if ($andNew) {
            // Reset form for a new question in the same group
            $this->reset([
                'questionId',
                'question_code',
                'primary_skill_id',
                'sub_skill_id',
                'difficulty_level_id',
                'age_group_id',
                'answer_category',
                'explaination',
                'admin_notes',
            ]);
            $this->question_content = [
                'question_text' => '',
                'options'       => [],
                'correct_answer'=> null,
            ];
            $this->question_code = $this->generateQuestionCode();
            $this->isLocked      = false;
            $this->question      = null;
        } else {
            $this->dispatch('closeQuestionForm');
        }
    }

    public function saveAndCreateNew(): void
    {
        $this->saveQuestion(andNew: true);
    }

    // ─── Render ───────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.admin.questions.question-form');
    }
}
