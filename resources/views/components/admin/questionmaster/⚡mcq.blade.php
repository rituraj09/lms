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

new #[Layout('layouts.backend')]
class extends Component {
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = 'New Question';
    public ?int $eventID = null;

    // ─── Meta ────────────────────────────────────────────────
    public ?int   $questionId         = null;
    public string $code               = '';
    public ?int   $questionTypeId     = null;
    public ?int   $primarySkillTypeId = null;
    public ?int   $subSkillTypeId     = null;
    public ?int   $difficultyLevelId  = null;
    public ?int   $ageGroupId         = null;
    public ?int   $timeLimit          = null;
    public string $maxScore           = '1.00';
    public ?string $adminNotes        = null;
    public string $status             = 'draft';

    // ─── Question Content ────────────────────────────────────
    public array  $stem        = [];   // ['en' => '', 'as' => '', ...]
    public array  $explanation = [];
    public array  $options     = [];
    public float  $negativeMark      = 0;
    public bool   $isOptionsShuffle  = false;
    public string $selectionType     = 'single'; // single | multiple

    // ─── UI State ────────────────────────────────────────────
    public bool   $showPreview      = false;
    public bool   $showConfirmation = false;
    public string $activeTab        = 'en';
    public string $pendingStatus    = '';

    // ─── Computed / Static ───────────────────────────────────
    public array  $languages        = [];
    public array  $questionTypes    = [];
    public array  $primarySkillTypes = [];
    public array  $subSkillTypes    = [];
    public array  $difficultyLevels = [];
    public array  $ageGroups        = [];

    // ─── Validation Rules ────────────────────────────────────
    protected function rules(): array
    {
        $rules = [
            'code'               => 'required|string|max:100|unique:questions,code,' . ($this->questionId ?? 'NULL'),
            'questionTypeId'     => 'required|exists:question_types,id',
            'primarySkillTypeId' => 'required|exists:primary_skill_types,id',
            'subSkillTypeId'     => 'required|exists:sub_skill_types,id',
            'difficultyLevelId'  => 'required|exists:difficulty_levels,id',
            'ageGroupId'         => 'required|exists:age_groups,id',
            'maxScore'           => 'required|numeric|min:0|max:9999.99',
            'timeLimit'          => 'nullable|integer|min:1|max:65535',
            'status'             => 'required|in:draft,publish,unpublish',
            'negativeMark'       => 'required|numeric|min:0',
            'selectionType'      => 'required|in:single,multiple',
        ];

        // Stem: at least one language required
        foreach ($this->languages as $langCode => $lang) {
            $rules["stem.{$langCode}"] = 'nullable|string';
        }

        // Options validation
        foreach ($this->options as $idx => $option) {
            $rules["options.{$idx}.id"]         = 'required|string';
            $rules["options.{$idx}.is_correct"]  = 'boolean';
            $rules["options.{$idx}.weightage"]   = 'required|numeric|min:0|max:100';
            foreach ($this->languages as $langCode => $lang) {
                $rules["options.{$idx}.text.{$langCode}"] = 'nullable|string';
            }
        }

        return $rules;
    }

    protected array $messages = [
        'code.required'               => 'Question code is required.',
        'questionTypeId.required'     => 'Please select a question type.',
        'primarySkillTypeId.required' => 'Please select a primary skill.',
        'subSkillTypeId.required'     => 'Please select a sub skill.',
        'difficultyLevelId.required'  => 'Please select a difficulty level.',
        'ageGroupId.required'           => 'Please select an age group.',
        'maxScore.required'           => 'Max score is required.',
        'stem.en'                     => 'English stem is recommended.',
    ];

    // ─── Lifecycle ───────────────────────────────────────────
    public function mount(?int $questionId = null): void
    {
        $this->languages       = \App\Helper\Globals::LANGUAGES;
        $this->questionTypes   = QuestionType::select('id', 'name')->get()->toArray();
        $this->primarySkillTypes = PrimarySkillType::select('id', 'name')->get()->toArray();
        $this->subSkillTypes   = SubSkillType::select('id', 'name')->get()->toArray();
        $this->difficultyLevels = DifficultyLevel::select('id', 'name')->get()->toArray();
        $this->ageGroups        = AgeGroup::select('id', 'name')->get()->toArray();

        // Init multilang arrays
        foreach ($this->languages as $langCode => $lang) {
            $this->stem[$langCode]        = '';
            $this->explanation[$langCode] = '';
        }

        // Auto-generate code
        $this->code = strtoupper('Q-' . Str::random(8));

        // Seed default options
        $this->addOption();
        $this->addOption();

        // // Load existing question
        // if ($questionId) {
        //     $this->questionId = $questionId;
        //     $this->loadQuestion($questionId);
        // }
    }

    private function loadQuestion(int $id): void
    {
        $question = Question::findOrFail($id);

        $this->code               = $question->code;
        $this->questionTypeId     = $question->question_type_id;
        $this->primarySkillTypeId = $question->primary_skill_type_id;
        $this->subSkillTypeId     = $question->sub_skill_type_id;
        $this->timeLimit          = $question->time_limit;
        $this->maxScore           = $question->max_score;
        $this->adminNotes         = $question->admin_notes;
        $this->status             = $question->status;

        $content = $question->question_contents;

        $this->stem            = $content['stem']        ?? array_fill_keys(array_keys($this->languages), '');
        $this->explanation     = $content['explanation'] ?? array_fill_keys(array_keys($this->languages), '');
        $this->options         = $content['options']     ?? [];
        $this->negativeMark    = $content['negative_mark']    ?? 0;
        $this->isOptionsShuffle = $content['is_options_shuffle'] ?? false;
        $this->selectionType   = $content['selection_type']  ?? 'single';
    }

    // ─── Option Management ───────────────────────────────────
    public function addOption(): void
    {
        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $idx    = count($this->options);
        $label  = $labels[$idx] ?? chr(65 + $idx);

        $textArr = [];
        foreach ($this->languages as $langCode => $lang) {
            $textArr[$langCode] = '';
        }

        $this->options[] = [
            'id'         => $label,
            'text'       => $textArr,
            'weightage'  => 0,
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
            // Deselect all others
            foreach ($this->options as $i => &$opt) {
                $opt['is_correct'] = ($i === $index);
                $opt['weightage']  = ($i === $index) ? 1 : 0;
            }
        } else {
            $this->options[$index]['is_correct'] = !$this->options[$index]['is_correct'];
        }
    }

    public function updatedSelectionType(): void
    {
        // Reset when switching between single/multiple
        foreach ($this->options as &$opt) {
            $opt['is_correct'] = false;
            $opt['weightage']  = 0;
        }
    }

    // ─── Language Tab ────────────────────────────────────────
    public function setActiveTab(string $lang): void
    {
        $this->activeTab = $lang;
    }

    // ─── Preview ─────────────────────────────────────────────
    public function openPreview(): void
    {
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    // ─── Submit Flow ─────────────────────────────────────────
    public function initiateSubmit(string $status): void
    {
        $this->validate();
        $this->pendingStatus    = $status;
        $this->showConfirmation = true;
        $this->showPreview      = false;
    }

    public function cancelConfirmation(): void
    {
        $this->showConfirmation = false;
        $this->pendingStatus    = '';
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
        $this->title = "New Question";
        $this->createForm = 1;
    }
     protected function resetForm()
    {  $this->reset([
              'questionId', 'code', 'questionTypeId', 'primarySkillTypeId', 'subSkillTypeId', 'timeLimit', 'maxScore', 'adminNotes', 'status','stem', 'explanation', 'options', 'negativeMark', 'isOptionsShuffle', 'selectionType','activeTab',

        ]);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->eventID = null;
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
        {{-- resources/views/livewire/questions/question-form.blade.php --}}

<div class="question-form-wrapper">

    {{-- ══════════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-patch-question-fill text-primary me-2"></i>
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
            <button type="button" class="btn btn-outline-info btn-sm"
                    wire:click="openPreview">
                <i class="bi bi-eye me-1"></i> Preview
            </button>
            <a href="#" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Global Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form wire:submit.prevent>

        {{-- ══════════════════════════════════════════════════════
             ROW 1: Two Column Layout
        ══════════════════════════════════════════════════════ --}}
        <div class="row g-4">

            {{-- LEFT COLUMN ─────────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- ── Card: Basic Information ──────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-info-circle text-primary me-2"></i>Basic Information
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
                                    <input type="text"
                                           wire:model.blur="code"
                                           class="form-control @error('code') is-invalid @enderror"
                                           placeholder="Q-XXXXXXXX" />
                                    <button class="btn btn-outline-secondary" type="button"
                                            wire:click="$set('code', 'Q-' + Math.random().toString(36).substr(2,8).toUpperCase())"
                                            title="Regenerate">
                                        <i class="bi bi-arrow-clockwise"></i>
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
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-translate text-primary me-2"></i>Question Stem
                        </h6>
                        {{-- Language Tabs --}}
                        <ul class="nav nav-pills nav-sm mb-0">
                            @foreach ($languages as $langCode => $lang)
                                <li class="nav-item">
                                    <button type="button"
                                            wire:click="setActiveTab('{{ $langCode }}')"
                                            class="nav-link py-1 px-3 small
                                                   {{ $activeTab === $langCode ? 'active' : '' }}">
                                        <span class="me-1">{{ $lang['flag'] }}</span>
                                        {{ $lang['label'] }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        @foreach ($languages as $langCode => $lang)
                            <div class="{{ $activeTab === $langCode ? '' : 'd-none' }}">
                                <label class="form-label fw-medium small">
                                    {{ $lang['flag'] }} {{ $lang['label'] }} — Question Stem
                                </label>
                                <textarea wire:model="stem.{{ $langCode }}"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Enter question stem in {{ $lang['label'] }}..."></textarea>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Card: MCQ Options ─────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="mb-0 fw-semibold text-dark">
                                <i class="bi bi-list-check text-primary me-2"></i>
                                Answer Options
                                <span class="badge bg-primary ms-1">{{ count($options) }}</span>
                            </h6>

                            <div class="d-flex align-items-center gap-3">
                                {{-- Selection Type Toggle --}}
                                <div class="d-flex align-items-center gap-2 bg-light rounded p-1">
                                    <button type="button"
                                            wire:click="$set('selectionType', 'single')"
                                            class="btn btn-sm {{ $selectionType === 'single' ? 'btn-primary' : 'btn-light' }}">
                                        <i class="bi bi-record-circle me-1"></i>Single
                                    </button>
                                    <button type="button"
                                            wire:click="$set('selectionType', 'multiple')"
                                            class="btn btn-sm {{ $selectionType === 'multiple' ? 'btn-primary' : 'btn-light' }}">
                                        <i class="bi bi-check2-square me-1"></i>Multiple
                                    </button>
                                </div>

                                <button type="button"
                                        wire:click="addOption"
                                        class="btn btn-outline-primary btn-sm"
                                        @if(count($options) >= 8) disabled @endif>
                                    <i class="bi bi-plus-lg me-1"></i>Add Option
                                </button>
                            </div>
                        </div>

                        @if ($selectionType === 'multiple')
                            <div class="mt-2">
                                <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Multi-select: Students can choose multiple answers. Set weightage per correct option.
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-4">
                        @error('options')
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        <div class="options-list">
                            @foreach ($options as $index => $option)
                                <div class="option-card rounded-3 border mb-3 overflow-hidden
                                            {{ $option['is_correct'] ? 'border-success' : 'border-light' }}"
                                     wire:key="option-{{ $index }}">

                                    {{-- Option Header --}}
                                    <div class="option-header d-flex align-items-center gap-3 px-3 py-2
                                                {{ $option['is_correct'] ? 'bg-success-subtle' : 'bg-light' }}">

                                        {{-- Correct Toggle --}}
                                        <button type="button"
                                                wire:click="toggleCorrect({{ $index }})"
                                                class="btn btn-sm {{ $option['is_correct'] ? 'btn-success' : 'btn-outline-secondary' }} rounded-circle p-0"
                                                style="width:32px;height:32px;"
                                                title="{{ $option['is_correct'] ? 'Mark as Incorrect' : 'Mark as Correct' }}">
                                            @if ($selectionType === 'single')
                                                <i class="bi {{ $option['is_correct'] ? 'bi-record-circle-fill' : 'bi-circle' }}"></i>
                                            @else
                                                <i class="bi {{ $option['is_correct'] ? 'bi-check-square-fill' : 'bi-square' }}"></i>
                                            @endif
                                        </button>

                                        {{-- Option Label --}}
                                        <span class="badge bg-secondary fs-6 fw-bold" style="width:30px;height:30px;line-height:18px;">
                                            {{ $option['id'] }}
                                        </span>

                                        @if ($option['is_correct'])
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-lg me-1"></i>Correct
                                            </span>
                                        @endif

                                        <div class="ms-auto d-flex align-items-center gap-2">
                                            {{-- Weightage --}}
                                            <div class="d-flex align-items-center gap-1">
                                                <label class="small text-muted mb-0 text-nowrap">Weightage:</label>
                                                <input type="number"
                                                       wire:model="options.{{ $index }}.weightage"
                                                       class="form-control form-control-sm text-center"
                                                       style="width:70px;"
                                                       step="0.1" min="0" max="100"
                                                       placeholder="0" />
                                            </div>

                                            {{-- Remove --}}
                                            <button type="button"
                                                    wire:click="removeOption({{ $index }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Remove Option">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Option Body: Multilingual Text --}}
                                    <div class="option-body p-3">
                                        <ul class="nav nav-tabs nav-sm mb-2 border-bottom-0" role="tablist">
                                            @foreach ($languages as $langCode => $lang)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link py-1 px-2 small
                                                                   {{ $activeTab === $langCode ? 'active' : '' }}"
                                                            type="button"
                                                            wire:click="setActiveTab('{{ $langCode }}')">
                                                        {{ $lang['flag'] }} {{ $lang['label'] }}
                                                        @if(!empty($option['text'][$langCode]))
                                                            <i class="bi bi-check-circle-fill text-success ms-1" style="font-size:10px;"></i>
                                                        @endif
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>

                                        @foreach ($languages as $langCode => $lang)
                                            <div class="{{ $activeTab === $langCode ? '' : 'd-none' }}">
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
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Correct: <strong>{{ $this->getCorrectOptionsCount() }}</strong>
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-list-ol me-1"></i>
                                Total: <strong>{{ count($options) }}</strong>
                            </small>
                        </div>
                    </div>
                </div>

                {{-- ── Card: Explanation ─────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-lightbulb text-warning me-2"></i>Explanation
                            <span class="text-muted fw-normal small ms-1">(Optional)</span>
                        </h6>
                        <ul class="nav nav-pills nav-sm mb-0">
                            @foreach ($languages as $langCode => $lang)
                                <li class="nav-item">
                                    <button type="button"
                                            wire:click="setActiveTab('{{ $langCode }}')"
                                            class="nav-link py-1 px-3 small
                                                   {{ $activeTab === $langCode ? 'active' : '' }}">
                                        {{ $lang['flag'] }} {{ $lang['label'] }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        @foreach ($languages as $langCode => $lang)
                            <div class="{{ $activeTab === $langCode ? '' : 'd-none' }}">
                                <textarea wire:model="explanation.{{ $langCode }}"
                                          class="form-control"
                                          rows="3"
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
                            <i class="bi bi-trophy text-warning me-2"></i>Scoring Settings
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Time Limit --}}
                            <div class="  mb-3">
                                <label class="form-label fw-medium small">
                                    Time Limit
                                    <span class="text-muted">(seconds)</span>
                                </label>
                                <input type="number"
                                       wire:model.blur="timeLimit"
                                       class="form-control @error('timeLimit') is-invalid @enderror"
                                       min="1" max="65535"
                                       placeholder="e.g. 60" />
                                @error('timeLimit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        {{-- Max Score --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium small">
                                    Max Score <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       wire:model.blur="maxScore"
                                       class="form-control @error('maxScore') is-invalid @enderror"
                                       step="0.01" min="0" max="9999.99"
                                       placeholder="1.00" />
                                @error('maxScore')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        {{-- Negative Mark --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium small">Negative Mark</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger-subtle text-danger">
                                    <i class="bi bi-dash-circle"></i>
                                </span>
                                <input type="number"
                                       wire:model.blur="negativeMark"
                                       class="form-control"
                                       step="0.01" min="0"
                                       placeholder="0.00" />
                            </div>
                            <div class="form-text small">Marks deducted for wrong answer</div>
                        </div>

                        {{-- Shuffle Options --}}
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model="isOptionsShuffle"
                                       id="shuffleCheck" />
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
                                <span class="fw-bold text-success">+{{ $maxScore }}</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Negative Mark</span>
                                <span class="fw-bold text-danger">-{{ $negativeMark }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between small">
                                <span class="fw-semibold">Correct Options</span>
                                <span class="fw-bold text-primary">{{ $this->getCorrectOptionsCount() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Card: Status & Publish ─────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-toggle-on text-primary me-2"></i>Status & Publishing
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Status Indicator --}}
                        <div class="text-center mb-4">
                            @php
                                $statusConfig = [
                                    'draft'     => ['color' => 'warning', 'icon' => 'bi-pencil-square', 'label' => 'Draft'],
                                    'publish'   => ['color' => 'success', 'icon' => 'bi-cloud-check', 'label' => 'Published'],
                                    'unpublish' => ['color' => 'secondary', 'icon' => 'bi-cloud-slash', 'label' => 'Unpublished'],
                                ];
                                $current = $statusConfig[$status] ?? $statusConfig['draft'];
                            @endphp
                            <div class="status-indicator mx-auto mb-2
                                        bg-{{ $current['color'] }}-subtle border border-{{ $current['color'] }}
                                        rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:64px;height:64px;">
                                <i class="bi {{ $current['icon'] }} text-{{ $current['color'] }} fs-4"></i>
                            </div>
                            <div class="fw-bold">Current Status</div>
                            <span class="badge bg-{{ $current['color'] }} mt-1 px-3">
                                {{ $current['label'] }}
                            </span>
                        </div>

                        <hr>

                        {{-- Status Radio Buttons --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium small">Change Status</label>

                            <div class="status-options d-flex flex-column gap-2">

                                <label class="status-option d-flex align-items-center gap-2 p-2 rounded border
                                              {{ $status === 'draft' ? 'border-warning bg-warning-subtle' : '' }}
                                              cursor-pointer">
                                    <input type="radio" wire:model="status" value="draft"
                                           class="form-check-input mt-0" />
                                    <span class="badge bg-warning text-dark">Draft</span>
                                    <span class="small text-muted">Save without publishing</span>
                                </label>

                                <label class="status-option d-flex align-items-center gap-2 p-2 rounded border
                                              {{ $status === 'publish' ? 'border-success bg-success-subtle' : '' }}
                                              cursor-pointer">
                                    <input type="radio" wire:model="status" value="publish"
                                           class="form-check-input mt-0" />
                                    <span class="badge bg-success">Publish</span>
                                    <span class="small text-muted">Make visible to students</span>
                                </label>

                                <label class="status-option d-flex align-items-center gap-2 p-2 rounded border
                                              {{ $status === 'unpublish' ? 'border-secondary bg-secondary-subtle' : '' }}
                                              cursor-pointer">
                                    <input type="radio" wire:model="status" value="unpublish"
                                           class="form-check-input mt-0" />
                                    <span class="badge bg-secondary">Unpublish</span>
                                    <span class="small text-muted">Hide from students</span>
                                </label>

                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2 mt-4">
                            <button type="button"
                                    wire:click="initiateSubmit('{{ $status }}')"
                                    wire:loading.attr="disabled"
                                    class="btn btn-primary">
                                <span wire:loading wire:target="initiateSubmit">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Validating...
                                </span>
                                <span wire:loading.remove wire:target="initiateSubmit">
                                    <i class="bi bi-save me-1"></i>
                                    Save as {{ ucfirst($status) }}
                                </span>
                            </button>

                            <button type="button"
                                    wire:click="openPreview"
                                    class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>Preview Question
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Card: Admin Notes ──────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-journal-text text-secondary me-2"></i>Admin Notes
                            <span class="text-muted fw-normal small ms-1">(Internal)</span>
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <textarea wire:model="adminNotes"
                                  class="form-control border-0 bg-light"
                                  rows="4"
                                  placeholder="Internal notes, not visible to students..."></textarea>
                    </div>
                </div>

            </div>{{-- /RIGHT COLUMN --}}
        </div>

    </form>



</div>

{{-- ── Styles ──────────────────────────────────────────────────── --}}
@push('styles')
<style>
    /* Option cards */
    .option-card { transition: all .2s ease; }
    .option-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }

    /* Status options */
    .status-option { cursor: pointer; transition: all .15s ease; }
    .status-option:hover { background-color: #f8f9fa; }

    /* Nav pills sm */
    .nav-sm .nav-link { font-size: .8rem; padding: .25rem .6rem; }

    /* Modal overlay */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 1055;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn .2s ease;
    }
    .modal-box {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp .25s ease;
    }
    @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
    @keyframes slideUp { from { transform:translateY(30px); opacity:0 } to { transform:translateY(0); opacity:1 } }

    /* Preview question styles */
    .preview-stem { font-size: 1.05rem; line-height: 1.7; }
    .preview-option {
        border: 2px solid #e9ecef;
        border-radius: .5rem;
        padding: .75rem 1rem;
        margin-bottom: .5rem;
        cursor: default;
        transition: all .15s;
    }
    .preview-option.correct {
        border-color: #198754;
        background: #d1e7dd;
    }
    .preview-option.incorrect {
        border-color: #e9ecef;
        background: #f8f9fa;
    }
</style>
@endpush
    @endif
    <div wire:loading>
        @include('utilities.backdrop')
    </div>
</div>
