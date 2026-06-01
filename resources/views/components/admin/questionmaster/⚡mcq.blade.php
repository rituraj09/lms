<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\EvaluationMaster\Question;
use App\Models\EvaluationMaster\QuestionType;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\SubSkillType;
use App\Models\EvaluationMaster\DifficultyLevel;
use App\Models\EvaluationMaster\AgeGroup;

new #[Layout('layouts.backend')] class extends Component {
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = 'New Question';
    public ?int $eventID = null;

    // ─── Meta ────────────────────────────────────────────────
    public ?int $questionId = null;
    public string $code = '';
    public ?int $questionTypeId = null;
    public ?int $primarySkillTypeId = null;
    public ?int $subSkillTypeId = null;
    public ?int $difficultyLevelId = null;
    public ?int $ageGroupId = null;
    public ?int $timeLimit = null;
    public string $maxScore = '1.00';
    public ?string $adminNotes = null;
    public string $status = 'draft';

    // ─── Question Content ────────────────────────────────────
    public array $stem = []; // ['en' => '', 'as' => '', ...]
    public array $explanation = [];
    public array $options = [];
    public float $negativeMark = 0;
    public bool $isOptionsShuffle = false;
    public string $selectionType = 'single'; // single | multiple

    // ─── UI State ────────────────────────────────────────────
    public string $activeTab = 'en';
    public string $pendingStatus = '';
    // ─── Computed / Static ───────────────────────────────────
    public array $languages = [];
    public array $questionTypes = [];
    public array $primarySkillTypes = [];
    public array $subSkillTypes = [];
    public array $difficultyLevels = [];
    public array $ageGroups = [];

    // ─── Validation Rules ────────────────────────────────────
    protected function rules(): array
    {
        $rules = [
            'code' => 'required|string|max:100|unique:questions,code,' . ($this->questionId ?? 'OT-001'),
            'questionTypeId' => 'required|exists:question_types,id',
            'primarySkillTypeId' => 'required|exists:primary_skill_types,id',
            'subSkillTypeId' => 'required|exists:sub_skill_types,id',
            'difficultyLevelId' => 'required|exists:difficulty_levels,id',
            'ageGroupId' => 'required|exists:age_groups,id',
            'maxScore' => 'required|numeric|min:0|max:9999.99',
            'timeLimit' => 'nullable|integer|min:1|max:65535',
            'status' => 'required|in:draft,publish,unpublish',
            'negativeMark' => 'required|numeric|min:0',
            'selectionType' => 'required|in:single,multiple',
        ];

        // Stem: at least one language required
        foreach ($this->languages as $langCode => $lang) {
            $rules["stem.{$langCode}"] = 'nullable|string';
        }

        // Options validation
        foreach ($this->options as $idx => $option) {
            $rules["options.{$idx}.id"] = 'required|string';
            $rules["options.{$idx}.is_correct"] = 'boolean';
            $rules["options.{$idx}.weightage"] = 'required|numeric|min:0|max:100';
            foreach ($this->languages as $langCode => $lang) {
                $rules["options.{$idx}.text.{$langCode}"] = 'nullable|string';
            }
        }

        return $rules;
    }

    protected array $messages = [
        'code.required' => 'Question code is required.',
        'questionTypeId.required' => 'Please select a question type.',
        'primarySkillTypeId.required' => 'Please select a primary skill.',
        'subSkillTypeId.required' => 'Please select a sub skill.',
        'difficultyLevelId.required' => 'Please select a difficulty level.',
        'ageGroupId.required' => 'Please select an age group.',
        'maxScore.required' => 'Max score is required.',
        'stem.en' => 'English stem is recommended.',
    ];

    // ─── Lifecycle ───────────────────────────────────────────
    public function mount(?int $questionId = null): void
    {
        $this->languages = \App\Helper\Globals::LANGUAGES;
        $this->questionTypes = QuestionType::select('id', 'name')->get()->toArray();
        $this->primarySkillTypes = PrimarySkillType::select('id', 'name')->get()->toArray();
        $this->subSkillTypes = SubSkillType::select('id', 'name')->get()->toArray();
        $this->difficultyLevels = DifficultyLevel::select('id', 'name')->get()->toArray();
        $this->ageGroups = AgeGroup::select('id', 'name')->get()->toArray();
        $this->initializeForm();
    }
    private function initializeForm(): void
    {
        $this->code = 'Q-' . strtoupper(Str::random(8));

        foreach ($this->languages as $langCode => $lang) {
            $this->stem[$langCode] = '';
            $this->explanation[$langCode] = '';
        }

        $this->options = [];

        $this->addOption();
        $this->addOption();
    }

    // ─── Option Management ───────────────────────────────────
    public function addOption(): void
    {
        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $idx = count($this->options);
        $label = $labels[$idx] ?? chr(65 + $idx);

        $textArr = [];
        foreach ($this->languages as $langCode => $lang) {
            $textArr[$langCode] = '';
        }

        $this->options[] = [
            'id' => $label,
            'text' => $textArr,
            'weightage' => 0,
            'is_correct' => false,
        ];
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) <= 2) {
            $this->addError('options', 'Minimum 2 options are required.');
            return;
        }
        array_splice($this->options, $index, 1);
        $this->reIndexOptions();
        $this->updateScoreSummary();
    }

    private function reIndexOptions(): void
    {
        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($this->options as $i => &$option) {
            $option['id'] = $labels[$i] ?? chr(65 + $i);
        }
    }

    public function toggleCorrect(int $index): void
    {
        if ($this->selectionType === 'single') {
            foreach ($this->options as $i => &$opt) {
                $opt['is_correct'] = $i === $index;
            }
        } else {
            $this->options[$index]['is_correct'] = !$this->options[$index]['is_correct'];
        }
        $this->updateScoreSummary();
    }

    private function updateScoreSummary(): void
    {
        $this->maxScore = collect($this->options)->filter(fn($option) => !empty($option['is_correct']))->sum(fn($option) => (float) ($option['weightage'] ?? 0));
    }
    public function updatedOptions(): void
    {
        $this->updateScoreSummary();
    }
    public function updated($property): void
    {
        if (str_starts_with($property, 'options.')) {
            $this->updateScoreSummary();
        }
    }

    public function updatedSelectionType(): void
    {
        // Reset when switching between single/multiple
        foreach ($this->options as &$opt) {
            $opt['is_correct'] = false;
            $opt['weightage'] = 0;
        }
    }

    // Count Score based on options
    public function getCalculatedMaxScoreProperty(): float
    {
        return collect($this->options)
            ->where('is_correct', true)
            ->sum(function ($option) {
                return (float) ($option['weightage'] ?? 0);
            });
    }

    public function getCalculatedNegativeMarkProperty(): float
    {
        return 0; // change logic later if needed
    }
    // ─── Preview ─────────────────────────────────────────────
    public function openPreview(): void
    {
        $this->validate();
        $this->createForm = 3;
    }

    public function closePreview(): void
    {
        $this->createForm = 1;
    }

    // ─── Submit Flow ─────────────────────────────────────────
    public function initiateSubmit(string $status): void
    {
        $this->pendingStatus = $status;
        $this->submitQuestion();
    }

    //Submit and save function

    // ─── Helpers for View ────────────────────────────────────
    public function getCorrectOptionsCount(): int
    {
        return collect($this->options)->where('is_correct', true)->count();
    }

    #[On('new-entry')]
    public function newEntry(): void
    {
        $this->resetForm();
        $this->initializeForm();
        $this->title = 'New Question';
        $this->createForm = 1;
    }
    protected function resetForm()
    {
        $this->reset(['questionId', 'code', 'questionTypeId', 'primarySkillTypeId', 'subSkillTypeId', 'timeLimit', 'maxScore', 'adminNotes', 'status', 'stem', 'explanation', 'options', 'negativeMark', 'isOptionsShuffle', 'selectionType', 'activeTab']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->eventID = null;
    }

    public function submitQuestion(): void
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $questionContents = [
                'stem' => $this->stem,
                'explanation' => $this->explanation,
                'options' => $this->options,
                'negative_mark' => $this->negativeMark,
                'is_options_shuffle' => $this->isOptionsShuffle,
                'selection_type' => $this->selectionType,
            ];

            // Convert the array to JSON string
            $questionContentsJson = json_encode($questionContents);

            // Check if JSON encoding was successful
            if ($questionContentsJson === false) {
                throw new \Exception('Failed to encode question contents to JSON');
            }

            // Prepare data for updateOrCreate
            $data = [
                'code' => $this->code,
                'question_type_id' => $this->questionTypeId,
                'primary_skill_type_id' => $this->primarySkillTypeId,
                'sub_skill_type_id' => $this->subSkillTypeId,
                'difficulty_level_id' => $this->difficultyLevelId,
                'age_group_id' => $this->ageGroupId,
                'question_contents' => $questionContentsJson, // Store as JSON string
                'time_limit' => $this->timeLimit,
                'max_score' => $this->maxScore,
                'admin_notes' => $this->adminNotes,
                'status' => $this->pendingStatus ?: $this->status,
            ];

            // If this is an update, include the ID
            if ($this->questionId) {
                $data['id'] = $this->questionId;
            }

            Question::updateOrCreate(['id' => $this->questionId], $data);

            DB::commit();

            $this->createForm = 1;

            session()->flash('success', 'Question saved successfully.');

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Failed to save question: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $questionContents ?? null,
            ]);

            $this->addError('save', 'Failed to save question. ' . $e->getMessage());
        }
    }
};
?>

<div>
    @if ($createForm == 0)
        <livewire:datatable model="App\Models\EvaluationMaster\Question" title="Questions" :new-entry="true"
            :columns="[
                ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'searchable' => true],
                ['key' => 'question_type.name', 'label' => 'Question Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'admin_notes', 'label' => 'Admin Notes', 'sortable' => true, 'searchable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]" :actions="[
                [
                    'label' => 'View',
                    'icon' => 'icon-base ri ri-focus-2-line',
                    'event' => 'viewOrganisation',
                    'class' => 'btn-outline-success',
                ],
                [
                    'label' => 'Edit',
                    'icon' => 'icon-base ri ri-edit-line',
                    'event' => 'edit',
                    'class' => 'btn-outline-primary',
                ],
                [
                    'label' => 'Delete',
                    'icon' => 'icon-base ri ri-delete-bin-4-line',
                    'event' => 'delete',
                    'class' => 'btn-outline-danger',
                    'confirm' => true,
                ],
            ]" />
    @elseif($createForm == 1)
        <div class="question-form-wrapper">


            {{-- ══════════════════════════════════════════════════════════
                PAGE HEADER  ══════════════════════════════════════════════════════════ --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="ri ri-questionnaire-fill text-primary me-2"></i>
                        {{ $questionId ? 'Edit Question' : 'Create New Question' }}
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Questions</a></li>
                            <li class="breadcrumb-item active">{{ $questionId ? 'Edit' : 'Create' }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">

                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            {{-- Flash Message --}}
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="ri ri-checkbox-circle-line me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Global Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri ri-error-warning-fill me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <div x-data="{ activeTab: 'en' }">
                <form wire:submit.prevent>
                    {{-- ══════════════════════════════════════════════════════
                        ROW 1: Two Column Layout ══════════════════════════════════════════════════════ --}}
                    <div class="row g-4">
                        {{-- LEFT COLUMN ─────────────────────────────────── --}}
                        <div class="col-lg-8">

                            {{-- ── Card: Basic Information ──────────────── --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="ri ri-information-line text-primary me-2"></i>Basic Information
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">

                                        {{-- Code --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small">
                                                Question Code <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" wire:model.blur="code"
                                                    class="form-control @error('code') is-invalid @enderror"
                                                    placeholder="Q-XXXXXXXX" />
                                                <button class="btn btn-outline-secondary" type="button"
                                                    wire:click="$set('code', 'Q-' + Math.random().toString(36).substr(2,8).toUpperCase())"
                                                    title="Regenerate">
                                                    <i class="ri ri-reset-right-fill"></i>
                                                </button>
                                                @error('code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Question Type --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small">
                                                Question Type <span class="text-danger">*</span>
                                            </label>
                                            <select wire:model="questionTypeId"
                                                class="form-select @error('questionTypeId') is-invalid @enderror">
                                                <option value="">— Select Type —</option>
                                                @foreach ($questionTypes as $type)
                                                    <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('questionTypeId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>



                                        {{-- Primary Skill --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small">
                                                Primary Skill <span class="text-danger">*</span>
                                            </label>
                                            <select wire:model="primarySkillTypeId"
                                                class="form-select @error('primarySkillTypeId') is-invalid @enderror">
                                                <option value="">— Select Skill —</option>
                                                @foreach ($primarySkillTypes as $skill)
                                                    <option value="{{ $skill['id'] }}">{{ $skill['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('primarySkillTypeId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Sub Skill --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small">
                                                Sub Skill <span class="text-danger">*</span>
                                            </label>
                                            <select wire:model="subSkillTypeId"
                                                class="form-select @error('subSkillTypeId') is-invalid @enderror">
                                                <option value="">— Select Sub Skill —</option>
                                                @foreach ($subSkillTypes as $skill)
                                                    <option value="{{ $skill['id'] }}">{{ $skill['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('subSkillTypeId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Difficulty Level --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small">
                                                Difficulty Level <span class="text-danger">*</span>
                                            </label>
                                            <select wire:model="difficultyLevelId"
                                                class="form-select @error('difficultyLevelId') is-invalid @enderror">
                                                <option value="">— Select Difficulty Level —</option>
                                                @foreach ($difficultyLevels as $level)
                                                    <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('difficultyLevelId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Age Group --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium small">
                                                Age Group <span class="text-danger">*</span>
                                            </label>
                                            <select wire:model="ageGroupId"
                                                class="form-select @error('ageGroupId') is-invalid @enderror">
                                                <option value="">— Select Age Group —</option>
                                                @foreach ($ageGroups as $group)
                                                    <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('ageGroupId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                    </div>
                                </div>
                            </div>

                            {{-- ── Card: Question Stem (Multilingual) ────── --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div
                                    class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="ri ri-translate-2 me-2"></i>Question Stem
                                    </h6>
                                    {{-- Language Tabs --}}
                                    <ul class="nav nav-pills nav-sm mb-0">
                                        @foreach ($languages as $langCode => $lang)
                                            <li class="nav-item">
                                                <button type="button" @click="activeTab='{{ $langCode }}'"
                                                    class="nav-link"
                                                    :class="{ 'active': activeTab === '{{ $langCode }}' }">
                                                    {{ $lang['flag'] }} {{ $lang['label'] }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body p-4">
                                    @foreach ($languages as $langCode => $lang)
                                        <div x-show="activeTab === '{{ $langCode }}'" x-cloak>
                                            <label class="form-label fw-medium small">
                                                {{ $lang['flag'] }} {{ $lang['label'] }} — Question Stem
                                            </label>
                                        </div>
                                        <div x-show="activeTab==='{{ $langCode }}'">
                                            <textarea wire:model="stem.{{ $langCode }}" class="form-control" rows="4"
                                                placeholder="Enter question stem in {{ $lang['label'] }}...">
                                            </textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ── Card: MCQ Options ─────────────────────── --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <h6 class="mb-0 fw-semibold text-dark">
                                            <i class="ri ri-list-check-3 text-primary me-2"></i>
                                            Answer Options
                                            <span class="badge bg-primary ms-1">{{ count($options) }}</span>
                                        </h6>

                                        <div class="d-flex align-items-center gap-3">
                                            {{-- Selection Type Toggle --}}
                                            <div class="d-flex align-items-center gap-2 bg-light rounded p-1">
                                                <button type="button" wire:click="$set('selectionType', 'single')"
                                                    class="btn btn-sm {{ $selectionType === 'single' ? 'btn-primary' : 'btn-light' }}">
                                                    <i class="ri ri-record-circle-line me-1"> </i>Single
                                                </button>
                                                <button type="button" wire:click="$set('selectionType', 'multiple')"
                                                    class="btn btn-sm {{ $selectionType === 'multiple' ? 'btn-primary' : 'btn-light' }}">
                                                    <i class="ri ri-checkbox-circle-line me-1"></i>Multiple
                                                </button>
                                            </div>

                                            <button type="button" wire:click="addOption"
                                                class="btn btn-outline-primary btn-sm"
                                                @if (count($options) >= 8) disabled @endif>
                                                <i class="ri ri-add-large-line me-1"></i>Add Option
                                            </button>
                                        </div>
                                    </div>

                                    @if ($selectionType === 'multiple')
                                        <div class="mt-2">
                                            <span
                                                class="badge bg-info-subtle text-info border border-info-subtle small">
                                                <i class="ri ri-information-line me-1"></i>
                                                Multi-select: Students can choose multiple answers. Set weightage per
                                                correct option.
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body p-4">
                                    @error('options')
                                        <div class="alert alert-warning py-2 small mb-3">
                                            <i class="ri ri-error-warning-fill me-1"></i>{{ $message }}
                                        </div>
                                    @enderror

                                    <div class="options-list">
                                        @foreach ($options as $index => $option)
                                            <div class="option-card rounded-3 border mb-3 overflow-hidden
                                                {{ $option['is_correct'] ? 'border-success' : 'border-light' }}"
                                                wire:key="option-{{ $index }}">

                                                {{-- Option Header --}}
                                                <div
                                                    class="option-header d-flex align-items-center gap-3 px-3 py-2
                                                    {{ $option['is_correct'] ? 'bg-success-subtle' : 'bg-light' }}">

                                                    {{-- Correct Toggle --}}
                                                    <button type="button"
                                                        wire:click="toggleCorrect({{ $index }})"
                                                        class="btn btn-sm {{ $option['is_correct'] ? 'btn-success' : 'btn-outline-secondary' }} rounded-circle p-0"
                                                        style="width:32px;height:32px;"
                                                        title="{{ $option['is_correct'] ? 'Mark as Incorrect' : 'Mark as Correct' }}">
                                                        @if ($selectionType === 'single')
                                                            <i
                                                                class="ri {{ $option['is_correct'] ? 'ri-record-circle-fill' : 'ri-circle-line' }}"></i>
                                                        @else
                                                            <i
                                                                class="ri {{ $option['is_correct'] ? 'ri-checkbox-circle-fill' : 'ri-checkbox-circle-line' }}"></i>
                                                        @endif
                                                    </button>

                                                    {{-- Option Label --}}
                                                    <span class="badge bg-secondary fs-6 fw-bold"
                                                        style="width:30px;height:30px;line-height:18px;">
                                                        {{ $option['id'] }}
                                                    </span>

                                                    @if ($option['is_correct'])
                                                        <span class="badge bg-success">
                                                            <i class="ri ri-check-fill me-1"></i>Correct
                                                        </span>
                                                    @endif

                                                    <div class="ms-auto d-flex align-items-center gap-2">
                                                        {{-- Weightage --}}
                                                        <div class="d-flex align-items-center gap-1">
                                                            <label
                                                                class="small text-muted mb-0 text-nowrap">Weightage:</label>

                                                            <input type="number"
                                                                wire:model.live="options.{{ $index }}.weightage"
                                                                class="form-control form-control-sm text-center"
                                                                style="width:70px;" step="0.1" min="0"
                                                                max="100" placeholder="0" />
                                                        </div>

                                                        {{-- Remove --}}
                                                        <button type="button"
                                                            wire:click="removeOption({{ $index }})"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Remove Option">
                                                            <i class="ri ri-delete-bin-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Option Body: Multilingual Text --}}
                                                <div class="option-body p-3">


                                                    @foreach ($languages as $langCode => $lang)
                                                        <div x-show="activeTab==='{{ $langCode }}'">
                                                            <input type="text"
                                                                wire:model="options.{{ $index }}.text.{{ $langCode }}"
                                                                class="form-control form-control-sm"
                                                                placeholder="Option {{ $option['id'] }} text in {{ $lang['label'] }}..." />
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Options Summary --}}
                                    <div class="d-flex gap-3 mt-2 pt-2 border-top">
                                        <small class="text-muted">
                                            <i class="ri ri-check-circle-fill text-success me-1"></i>
                                            Correct: <strong>{{ $this->getCorrectOptionsCount() }}</strong>
                                        </small>
                                        <small class="text-muted">
                                            <i class="ri ri-list-ordered-2 me-1"></i>
                                            Total: <strong>{{ count($options) }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Card: Explanation ─────────────────────── --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div
                                    class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="ri ri-lightbulb-fill text-warning me-2"></i>Explanation
                                        <span class="text-muted fw-normal small ms-1">(Optional)</span>
                                    </h6>

                                </div>
                                <div class="card-body p-4">
                                    @foreach ($languages as $langCode => $lang)
                                        <div x-show="activeTab==='{{ $langCode }}'">
                                            <textarea wire:model="explanation.{{ $langCode }}" class="form-control" rows="3"
                                                placeholder="Explain the correct answer in {{ $lang['label'] }}..."></textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>{{-- /LEFT COLUMN --}}

                        {{-- RIGHT COLUMN ────────────────────────────────── --}}
                        <div class="col-lg-4">

                            {{-- ── Card: Scoring Settings ────────────────── --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="ri ri-trophy-fill text-warning me-2"></i>Scoring Settings
                                    </h6>
                                </div>
                                <div class="card-body p-4">

                                    {{-- Time Limit --}}
                                    <div class="  mb-3">
                                        <label class="form-label fw-medium small">
                                            Time Limit
                                            <span class="text-muted">(seconds)</span>
                                        </label>
                                        <input type="number" wire:model.blur="timeLimit"
                                            class="form-control @error('timeLimit') is-invalid @enderror"
                                            min="1" max="65535" placeholder="e.g. 60" />
                                        @error('timeLimit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Max Score --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small">
                                            Max Score <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" value="{{ $maxScore }}"
                                            readonly />
                                        @error('maxScore')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Negative Mark --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small">Negative Mark</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-danger-subtle text-danger">

                                                <i class="ri ri-indeterminate-circle-fill"></i>
                                            </span>
                                            <input type="number" wire:model.live="negativeMark" class="form-control"
                                                step="0.01" min="0" placeholder="0.00" />

                                        </div>
                                        <div class="form-text small">Marks deducted for wrong answer</div>
                                    </div>

                                    {{-- Shuffle Options --}}
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="isOptionsShuffle" id="shuffleCheck" />
                                            <label class="form-check-label fw-medium small" for="shuffleCheck">
                                                Shuffle Options
                                            </label>
                                        </div>
                                        <div class="form-text small">Randomize option order for each student</div>
                                    </div>

                                    {{-- Score Summary --}}
                                    <div class="bg-light rounded p-3 mt-3">
                                        <div class="small fw-semibold mb-2 text-dark">Score Summary</div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Max Score</span>
                                            <span class="fw-bold text-success">
                                                +{{ number_format((float) $maxScore, 2) }}
                                            </span>

                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Negative Mark</span>
                                            <span class="fw-bold text-danger">
                                                -{{ number_format((float) $negativeMark, 2) }}
                                            </span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between small">
                                            <span class="fw-semibold">Correct Options</span>
                                            <span
                                                class="fw-bold text-primary">{{ $this->getCorrectOptionsCount() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            {{-- ── Card: Admin Notes ──────────────────────── --}}
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="ri ri-newspaper-fill text-secondary me-2"></i>Admin Notes
                                        <span class="text-muted fw-normal small ms-1">(Internal)</span>
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <textarea wire:model="adminNotes" class="form-control border-0 bg-light" rows="4"
                                        placeholder="Internal notes, not visible to students..."></textarea>
                                </div>
                                <div class="card-footer p-3">

                                    <button type="button" class="btn btn-outline-info" wire:loading.attr="disabled">
                                        <span wire:loading wire:target="openPreview">
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                            Validating...
                                        </span>
                                        <span wire:loading.remove wire:click="openPreview">
                                            <i class="ri ri-eye-fill me-1"></i>Preview Question
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </div>{{-- /RIGHT COLUMN --}}
                    </div>
                </form>
            </div>
        </div>
    @elseif ($createForm == 3)
        {{-- Professional Preview Panel --}}
        <div class="container-fluid px-0">
            {{-- Header with breadcrumb --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="ri ri-eye-fill text-primary me-2"></i>
                        Question Preview
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="#" wire:click="closePreview">Question Form</a>
                            </li>
                            <li class="breadcrumb-item active">Preview & Publish</li>
                        </ol>
                    </nav>
                </div>
                <button type="button" class="btn btn-outline-secondary" wire:click="closePreview">
                    <i class="ri ri-arrow-left-line me-1"></i> Back to Edit
                </button>
            </div>

            <div class="row g-4">
                {{-- LEFT COLUMN: Question Preview --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0" x-data="{ previewTab: '{{ array_key_first($languages) }}' }">
                        {{-- Card Header with Language Tabs --}}
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h6 class="mb-0 fw-semibold text-dark">
                                    <i class="ri ri-file-copy-line text-primary me-2"></i>
                                    Question Content
                                </h6>
                                @if (count($languages) > 1)
                                    <ul class="nav nav-pills nav-sm mb-0">
                                        @foreach ($languages as $langCode => $lang)
                                            <li class="nav-item">
                                                <button type="button" @click="previewTab = '{{ $langCode }}'"
                                                    class="nav-link"
                                                    :class="{ 'active': previewTab === '{{ $langCode }}' }">
                                                    {{ $lang['flag'] }} {{ $lang['label'] }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-4">
                            {{-- Question Stem --}}
                            <div class="preview-section mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                        <i class="ri ri-question-line text-primary"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">Question Stem</h6>
                                </div>
                                <div class="bg-light rounded p-4">
                                    @foreach ($languages as $langCode => $lang)
                                        <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                                            <p class="mb-0 fs-5 fw-medium">
                                                {{ $stem[$langCode] ?: $stem[array_key_first($languages)] ?? 'No question stem available' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Answer Options --}}
                            <div class="preview-section mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="ri ri-list-check-3 text-success"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">Answer Options</h6>
                                    <span class="badge bg-info ms-2">
                                        <i
                                            class="ri ri-{{ $selectionType === 'single' ? 'record-circle' : 'checkbox-circle' }}-line me-1"></i>
                                        {{ $selectionType === 'single' ? 'Single Select' : 'Multiple Select' }}
                                    </span>
                                    @if ($isOptionsShuffle)
                                        <span class="badge bg-secondary">
                                            <i class="ri ri-shuffle-line me-1"></i>Shuffle
                                        </span>
                                    @endif
                                </div>

                                <div class="options-list">
                                    @foreach ($options as $index => $option)
                                        <div
                                            class="option-preview-item mb-3 p-3 rounded border
                                        {{ $option['is_correct'] ? 'border-success bg-success bg-opacity-10' : 'border-secondary' }}">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="option-badge rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width: 40px; height: 40px;">
                                                    <span class="fw-bold fs-5">{{ $option['id'] }}</span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    @foreach ($languages as $langCode => $lang)
                                                        <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                                                            <p class="mb-0">
                                                                {{ $option['text'][$langCode] ?: ($option['text'][array_key_first($languages)] ?: 'No text available') }}
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if ($option['is_correct'])
                                                    <div class="flex-shrink-0">
                                                        <span class="badge bg-success px-3 py-2">
                                                            <i class="ri ri-check-fill me-1"></i> Correct
                                                            @if ($selectionType === 'multiple' && $option['weightage'] > 0)
                                                                ({{ number_format($option['weightage'], 1) }} pts)
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Explanation --}}
                            @php
                                $hasExplanation = false;
                                foreach ($languages as $langCode => $lang) {
                                    if (!empty($explanation[$langCode])) {
                                        $hasExplanation = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if ($hasExplanation)
                                <div class="preview-section">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                                            <i class="ri ri-lightbulb-fill text-warning"></i>
                                        </div>
                                        <h6 class="mb-0 fw-semibold">Explanation</h6>
                                    </div>
                                    <div class="alert alert-info border-0">
                                        @foreach ($languages as $langCode => $lang)
                                            <div x-show="previewTab === '{{ $langCode }}'" x-cloak>
                                                {!! nl2br(e($explanation[$langCode] ?? ($explanation[array_key_first($languages)] ?? ''))) !!}
                                                @if (empty($explanation[$langCode]) && $loop->first)
                                                    <em class="text-muted">No explanation available in
                                                        {{ $lang['label'] }}</em>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Status & Actions --}}
                <div class="col-lg-4">
                    {{-- Score Summary Card --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-trophy-fill text-warning me-2"></i>Score Summary
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Max Score</div>
                                        <div class="fs-3 fw-bold text-success">
                                            {{ number_format((float) $maxScore, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Negative Mark</div>
                                        <div class="fs-3 fw-bold text-danger">
                                            {{ number_format((float) $negativeMark, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="small text-muted mb-1">Correct Options</div>
                                        <div class="fs-2 fw-bold text-primary">
                                            {{ $this->getCorrectOptionsCount() }} / {{ count($options) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($timeLimit || $adminNotes)
                                <hr class="my-3">
                                <div class="mt-2">
                                    @if ($timeLimit)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="ri ri-timer-line text-muted"></i>
                                            <span class="small text-muted">Time Limit:</span>
                                            <strong>{{ $timeLimit }} seconds</strong>
                                        </div>
                                    @endif
                                    @if ($adminNotes)
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="ri ri-newspaper-line text-muted mt-1"></i>
                                            <div>
                                                <span class="small text-muted d-block">Admin Notes:</span>
                                                <small class="text-muted">{{ Str::limit($adminNotes, 100) }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Quick Info Card --}}
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-information-line text-info me-2"></i>Quick Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-2">
                                <small class="text-muted d-block">Question Code</small>
                                <strong>{{ $code }}</strong>
                            </div>
                            @if ($questionTypeId)
                                <div class="mb-2">
                                    <small class="text-muted d-block">Question Type</small>
                                    <strong>{{ collect($questionTypes)->firstWhere('id', $questionTypeId)['name'] ?? 'N/A' }}</strong>
                                </div>
                            @endif
                            @if ($difficultyLevelId)
                                <div class="mb-2">
                                    <small class="text-muted d-block">Difficulty Level</small>
                                    <strong>{{ collect($difficultyLevels)->firstWhere('id', $difficultyLevelId)['name'] ?? 'N/A' }}</strong>
                                </div>
                            @endif
                            @if ($ageGroupId)
                                <div>
                                    <small class="text-muted d-block">Age Group</small>
                                    <strong>{{ collect($ageGroups)->firstWhere('id', $ageGroupId)['name'] ?? 'N/A' }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- Status & Publishing Card --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="ri ri-toggle-fill text-primary me-2"></i>Status & Publishing
                            </h6>
                        </div>
                        <div class="card-body p-4" x-data="{
                            selectedStatus: @entangle('pendingStatus'),
                            init() {
                                if (!this.selectedStatus) this.selectedStatus = 'draft';
                            }
                        }">
                            {{-- Status Preview --}}
                            <div class="text-center mb-4">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;"
                                    :class="{
                                        'bg-warning bg-opacity-10': selectedStatus == 'draft',
                                        'bg-success bg-opacity-10': selectedStatus == 'publish',
                                        'bg-secondary bg-opacity-10': selectedStatus == 'unpublish'
                                    }">
                                    <i class="fs-1"
                                        :class="{
                                            'ri-edit-box-line text-warning': selectedStatus == 'draft',
                                            'ri-cloud-line text-success': selectedStatus == 'publish',
                                            'ri-cloud-off-line text-secondary': selectedStatus == 'unpublish'
                                        }">
                                    </i>
                                </div>
                                <div class="fw-semibold mb-1">Status to be saved as</div>
                                <span class="badge px-3 py-2 fs-6"
                                    :class="{
                                        'bg-warning text-dark': selectedStatus == 'draft',
                                        'bg-success': selectedStatus == 'publish',
                                        'bg-secondary': selectedStatus == 'unpublish'
                                    }"
                                    x-text="selectedStatus == 'draft' ? 'Draft' : (selectedStatus == 'publish' ? 'Published' : 'Unpublished')">
                                </span>
                            </div>

                            <hr>

                            {{-- Status Selection --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium small mb-2">Change Status</label>
                                <div class="d-flex flex-column gap-2">
                                    <label
                                        class="status-option d-flex align-items-center gap-3 p-3 rounded border cursor-pointer transition"
                                        :class="selectedStatus == 'draft' ? 'border-warning bg-warning bg-opacity-10' :
                                            'border-secondary'">
                                        <input type="radio" x-model="selectedStatus" value="draft"
                                            class="form-check-input">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">Draft</div>
                                            <div class="small text-muted">Question is not visible to students</div>
                                        </div>
                                        <i class="ri-edit-box-line text-warning fs-4"></i>
                                    </label>

                                    <label
                                        class="status-option d-flex align-items-center gap-3 p-3 rounded border cursor-pointer transition"
                                        :class="selectedStatus == 'publish' ? 'border-success bg-success bg-opacity-10' :
                                            'border-secondary'">
                                        <input type="radio" x-model="selectedStatus" value="publish"
                                            class="form-check-input">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">Publish</div>
                                            <div class="small text-muted">Question is visible to students</div>
                                        </div>
                                        <i class="ri-cloud-line text-success fs-4"></i>
                                    </label>

                                    <label
                                        class="status-option d-flex align-items-center gap-3 p-3 rounded border cursor-pointer transition"
                                        :class="selectedStatus == 'unpublish' ? 'border-secondary bg-secondary bg-opacity-10' :
                                            'border-secondary'">
                                        <input type="radio" x-model="selectedStatus" value="unpublish"
                                            class="form-check-input">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">Unpublish</div>
                                            <div class="small text-muted">Question is hidden from students</div>
                                        </div>
                                        <i class="ri-cloud-off-line text-secondary fs-4"></i>
                                    </label>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-grid gap-2 mt-4">
                                <button type="button" class="btn btn-primary btn-lg" wire:click="submitQuestion">
                                    <i class="ri ri-save-fill me-2"></i>
                                    Save as <span
                                        x-text="selectedStatus == 'draft' ? 'Draft' : (selectedStatus == 'publish' ? 'Published' : 'Unpublished')"></span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" wire:click="closePreview">
                                    <i class="ri ri-arrow-go-back-line me-1"></i>
                                    Back to Edit
                                </button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        {{-- Custom Styles --}}
        @push('styles')
            <style>
                .preview-section {
                    animation: fadeIn 0.3s ease;
                }

                .option-preview-item {
                    transition: all 0.2s ease;
                }

                .option-preview-item:hover {
                    transform: translateX(5px);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }

                .status-option {
                    transition: all 0.2s ease;
                    cursor: pointer;
                }

                .status-option:hover {
                    transform: translateX(5px);
                }

                .transition {
                    transition: all 0.2s ease;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                [x-cloak] {
                    display: none !important;
                }
            </style>
        @endpush
    @endif



    <div wire:loading>
        @include('utilities.backdrop')
    </div>

</div>
