<?php

use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\EvaluationMaster\Question;
use App\Models\EvaluationMaster\QuestionType;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\SubSkillType;
use App\Models\EvaluationMaster\DifficultyLevel;

new #[Layout('layouts.backend')] class extends Component {
    use WithFileUploads;

    // ── View state ───────────────────────────────────────────────
    public int $createForm = 0;
    public bool $is_edit = false;
    public string $title = 'Questions';
    public ?int $eventID = null;

    // Collections
    public $questionTypes = [];

    public $primarySkills;
    public $subSkills;
    public $difficultyLevels;

    // ── Active language tab ──────────────────────────────────────
    public string $activeLang = 'en';
    public array $languages = [
        'en' => ['label' => 'English', 'flag' => '🇬🇧'],
        'as' => ['label' => 'Assamese', 'flag' => '🇮🇳'],
        'bn' => ['label' => 'Bengali', 'flag' => '🇧🇩'],
    ];

    // ── Core fields ──────────────────────────────────────────────

    public ?int $question_type_id = null;
    public ?int $primary_skill_type_id = null;
    public ?int $sub_skill_type_id = null;
    public ?int $difficulty_level_id = null;
    public ?int $age_group_id = null;
    public ?int $time_limit = null;
    public float $max_score = 1.0;
    public string $status = 'draft';
    public string $admin_notes = '';

    // ── Multilingual question contents ───────────────────────────
    // stem[lang], explanation[lang]
    public array $stem = ['en' => '', 'as' => '', 'bn' => ''];
    public array $explanation = ['en' => '', 'as' => '', 'bn' => ''];

    // options: array of {id, text{en,as,bn}, is_correct, media}
    public array $options = [];

    // media
    public ?string $mediaType = null; // image|video|audio|null
    public ?string $mediaPath = null;
    public $mediaUpload = null;

    // ── Cascade dropdowns ────────────────────────────────────────
    public array $subSkillOptions = [];
    public array $difficultyOptions = [];

    // ── Validation messages ──────────────────────────────────────
    protected function messages(): array
    {
        return [
            'stem.en.required' => 'English question stem is required.',
            'options.*.text.en.required' => 'English text is required for all options.',
            'question_type_id.required' => 'Please select a question type.',
            'primary_skill_type_id.required' => 'Please select a primary skill.',
            'sub_skill_type_id.required' => 'Please select a sub-skill.',
        ];
    }

    protected function rules(): array
    {
        return [
            'question_type_id' => 'required|exists:question_types,id',
            'primary_skill_type_id' => 'required|exists:primary_skill_types,id',
            'sub_skill_type_id' => 'required|exists:sub_skill_types,id',
            'time_limit' => 'nullable|integer|min:5|max:3600',
            'max_score' => 'required|numeric|min:0.5|max:100',
            'status' => 'required|in:draft,publish,unpublish',
            'stem.en' => 'required|string|min:3',
            'stem.as' => 'nullable|string',
            'stem.bn' => 'nullable|string',
            'explanation.en' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.text.en' => 'required|string|min:1',
            'options.*.text.as' => 'nullable|string',
            'options.*.text.bn' => 'nullable|string',
            'options.*.is_correct' => 'boolean',
        ];
    }

    #[On('new-entry')]
    public function newEntry(): void
    {
        $this->resetForm();
        $this->title = 'New Question';
        $this->createForm = 1;
    }
    // ── Lifecycle ────────────────────────────────────────────────
    public function mount(): void
    {
        $this->addOption();
        $this->addOption();
        $this->addOption();
        $this->addOption();
        $this->questionTypes = QuestionType::where('id', 1)->get();
        $this->primarySkills = PrimarySkillType::all();
        $this->subSkills = SubSkillType::all();
        $this->difficultyLevels = DifficultyLevel::all();
    }

    // ── Watchers ─────────────────────────────────────────────────
    public function updatedPrimarySkillTypeId(): void
    {
        $this->sub_skill_type_id = null;
        $this->difficulty_level_id = null;
        $this->subSkillOptions = SubSkillType::where('primary_skill_type_id', $this->primary_skill_type_id)
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->toArray();
        $this->difficultyOptions = [];
    }

    public function updatedSubSkillTypeId(): void
    {
        $this->difficulty_level_id = null;
        $sub = SubSkillType::with('difficultyLevels')->find($this->sub_skill_type_id);
        $this->difficultyOptions = $sub?->difficultyLevels->toArray() ?? [];
        if ($sub?->age_group_id) {
            $this->age_group_id = $sub->age_group_id;
        }
    }

    // ── Option helpers ────────────────────────────────────────────
    public function addOption(): void
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        $idx = count($this->options);
        $this->options[] = [
            'id' => $letters[$idx] ?? 'OPT' . ($idx + 1),
            'text' => ['en' => '', 'as' => '', 'bn' => ''],
            'is_correct' => false,
            'media' => null,
        ];
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) <= 2) {
            return;
        }
        array_splice($this->options, $index, 1);
        // Re-letter
        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($this->options as $i => &$opt) {
            $opt['id'] = $letters[$i] ?? 'OPT' . ($i + 1);
        }
    }

    public function setCorrect(int $index): void
    {
        // MCQ = single correct; toggle exclusively
        foreach ($this->options as $i => &$opt) {
            $opt['is_correct'] = $i === $index;
        }
    }

    public function clearCorrect(int $index): void
    {
        $this->options[$index]['is_correct'] = false;
    }

    // ── Generate code ─────────────────────────────────────────────
    private function generateCode(): string
    {
        $last = Question::orderByDesc('id')->value('code');
        $n = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'QST_' . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    // ── Save ──────────────────────────────────────────────────────
    public function save(): void
    {
        $this->validate();

        // Ensure exactly one option is correct for MCQ
        $correctCount = collect($this->options)->where('is_correct', true)->count();
        if ($correctCount !== 1) {
            $this->addError('options', 'Please mark exactly one option as correct for MCQ.');
            return;
        }

        // Handle media upload
        $mediaPath = $this->mediaPath;
        if ($this->mediaUpload) {
            $mediaPath = $this->mediaUpload->store('questions/media', 'public');
        }

        $contents = [
            'stem' => array_filter($this->stem),
            'media' => $this->mediaType ? ['type' => $this->mediaType, 'path' => $mediaPath] : null,
            'options' => array_map(
                fn($opt) => [
                    'id' => $opt['id'],
                    'text' => array_filter($opt['text']),
                    'media' => $opt['media'],
                    'is_correct' => (bool) $opt['is_correct'],
                ],
                $this->options,
            ),
            'explanation' => array_filter($this->explanation),
        ];

        $data = [
            'code' => $this->is_edit ? optional(Question::find($this->eventID))->code : $this->generateCode(),
            'question_type_id' => $this->question_type_id,
            'primary_skill_type_id' => $this->primary_skill_type_id,
            'sub_skill_type_id' => $this->sub_skill_type_id,
            'question_contents' => $contents,
            'time_limit' => $this->time_limit ?: null,
            'max_score' => $this->max_score,
            'admin_notes' => $this->admin_notes,
            'status' => $this->status,
            'updated_by' => auth('admin')->id(),
        ];

        if ($this->is_edit) {
            Question::findOrFail($this->eventID)->update($data);
            session()->flash('success', 'Question updated successfully.');
        } else {
            $data['created_by'] = auth('admin')->id();
            Question::create($data);
            session()->flash('success', 'Question created successfully.');
        }

        $this->cancelForm();
    }

    // ── Edit / Delete listeners ───────────────────────────────────
    #[On('edit')]
    public function editEntry(int $id): void
    {
        $q = Question::findOrFail($id);
        $c = $q->question_contents;

        $this->is_edit = true;
        $this->eventID = $id;
        $this->title = 'Edit Question';
        $this->question_type_id = $q->question_type_id;
        $this->primary_skill_type_id = $q->primary_skill_type_id;

        $this->updatedPrimarySkillTypeId();
        $this->sub_skill_type_id = $q->sub_skill_type_id;
        $this->updatedSubSkillTypeId();

        $this->time_limit = $q->time_limit;
        $this->max_score = $q->max_score;
        $this->status = $q->status;
        $this->admin_notes = $q->admin_notes ?? '';
        $this->stem = array_merge(['en' => '', 'as' => '', 'bn' => ''], $c['stem'] ?? []);
        $this->explanation = array_merge(['en' => '', 'as' => '', 'bn' => ''], $c['explanation'] ?? []);
        $this->options = array_map(
            fn($o) => array_merge(
                [
                    'id' => '',
                    'text' => ['en' => '', 'as' => '', 'bn' => ''],
                    'is_correct' => false,
                    'media' => null,
                ],
                $o,
                ['text' => array_merge(['en' => '', 'as' => '', 'bn' => ''], $o['text'] ?? [])],
            ),
            $c['options'] ?? [],
        );
        $this->mediaType = $c['media']['type'] ?? null;
        $this->mediaPath = $c['media']['path'] ?? null;
        $this->createForm = 1;
    }

    #[On('delete')]
    public function deleteEntry(int $id): void
    {
        Question::findOrFail($id)->delete();
        session()->flash('success', 'Question deleted.');
    }

    // ── Cancel form ───────────────────────────────────────────────
    public function cancelForm(): void
    {
        $this->reset(['is_edit', 'eventID', 'question_type_id', 'primary_skill_type_id', 'sub_skill_type_id', 'difficulty_level_id', 'age_group_id', 'time_limit', 'max_score', 'status', 'admin_notes', 'stem', 'explanation', 'options', 'mediaType', 'mediaPath', 'mediaUpload', 'subSkillOptions', 'difficultyOptions']);
        $this->stem = ['en' => '', 'as' => '', 'bn' => ''];
        $this->explanation = ['en' => '', 'as' => '', 'bn' => ''];
        $this->max_score = 1.0;
        $this->status = 'draft';
        $this->activeLang = 'en';
        $this->title = 'Questions';
        $this->addOption();
        $this->addOption();
        $this->addOption();
        $this->addOption();
        $this->createForm = 0;
    }
    protected function resetForm()
    {
        $this->reset(['is_edit', 'eventID', 'question_type_id', 'primary_skill_type_id', 'sub_skill_type_id', 'difficulty_level_id', 'age_group_id', 'time_limit', 'max_score', 'status', 'admin_notes', 'stem', 'explanation', 'options', 'mediaType', 'mediaPath', 'mediaUpload', 'subSkillOptions', 'difficultyOptions']);
        $this->stem = ['en' => '', 'as' => '', 'bn' => ''];
        $this->explanation = ['en' => '', 'as' => '', 'bn' => ''];
        $this->max_score = 1.0;
        $this->status = 'draft';
        $this->activeLang = 'en';
        $this->title = 'Questions';
        $this->addOption();
        $this->addOption();
        $this->addOption();
        $this->addOption();
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
    }
    // public function render()
    // {
    //     return view('livewire.evaluation-master.questions', [
    //         'questionTypes' => QuestionType::where('is_active', true)->get(['id', 'name', 'slug']),
    //         'primarySkills' => PrimarySkillType::where('is_active', true)->get(['id', 'name']),
    //         'ageGroups' => \App\Models\EvaluationMaster\AgeGroup::where('is_active', true)->get(['id', 'name']),
    //     ]);
    // }
};
?>

<div>
    {{-- ── Flash messages ─────────────────────────────────────── --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="ri ri-checkbox-circle-line fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         LIST VIEW
    ══════════════════════════════════════════════════════════════ --}}
    @if ($createForm == 0)
        <livewire:datatable model="App\Models\EvaluationMaster\Question" title="Questions" :new-entry="true"
            :columns="[
                ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'searchable' => true],
                ['key' => 'question_type.name', 'label' => 'Question Type', 'sortable' => true, 'searchable' => true],
                ['key' => 'admin_notes', 'label' => 'Admin Notes', 'sortable' => true, 'searchable' => true],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]" :actions="[
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
    @endif

    {{-- ══════════════════════════════════════════════════════════
         FORM VIEW
    ══════════════════════════════════════════════════════════════ --}}
    @if ($createForm == 1)
        <div class="question-form-wrapper">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <button wire:click="cancelForm" class="btn btn-icon btn-outline-secondary rounded-circle">
                        <i class="ri ri-arrow-left-line"></i>
                    </button>
                    <div>
                        <h4 class="mb-0 fw-semibold">{{ $is_edit ? 'Edit Question' : 'New MCQ Question' }}</h4>
                        <p class="text-muted mb-0 small">Multiple Choice Question — multilingual content</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="cancelForm" class="btn btn-outline-secondary">
                        <i class="ri ri-close-line me-1"></i>Cancel
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary">
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></span>
                        <i wire:loading.remove wire:target="save" class="ri ri-save-line me-1"></i>
                        {{ $is_edit ? 'Update Question' : 'Save Question' }}
                    </button>
                </div>
            </div>

            <div class="row g-4">

                {{-- ── LEFT COLUMN: Classification ─────────────────── --}}
                <div class="col-lg-4">
                    <div class="card h-auto shadow-none border">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs rounded bg-label-primary">
                                    <i class="ri ri-bar-chart-box-line ri-sm"></i>
                                </span>
                                Classification
                            </h6>
                        </div>
                        <div class="card-body">

                            {{-- Question Type --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium">Question Type <span
                                        class="text-danger">*</span></label>
                                <select wire:model="question_type_id"
                                    class="form-select @error('question_type_id') is-invalid @enderror">
                                    @foreach ($questionTypes as $qt)
                                        <option value="{{ $qt->id }}">{{ $qt->name }}</option>
                                    @endforeach
                                </select>
                                @error('question_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Primary Skill --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium">Primary Skill <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="primary_skill_type_id"
                                    class="form-select @error('primary_skill_type_id') is-invalid @enderror">
                                    <option value="">— Select Skill —</option>
                                    @foreach ($primarySkills as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                    @endforeach
                                </select>
                                @error('primary_skill_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Sub Skill --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium">Sub Skill <span class="text-danger">*</span></label>
                                <select wire:model.live="sub_skill_type_id"
                                    class="form-select @error('sub_skill_type_id') is-invalid @enderror">
                                    <option value="">— Select Sub-Skill —</option>
                                    @foreach ($subSkillOptions as $ss)
                                        <option value="{{ $ss['id'] }}">{{ $ss['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('sub_skill_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>

                            {{-- Difficulty Level --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium">Difficulty Level</label>
                                <select wire:model="difficulty_level_id" class="form-select">
                                    <option value="">— Select Level —</option>
                                    @foreach ($difficultyLevels as $dl)
                                        <option value="{{ $dl['id'] }}">{{ $dl['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="my-3">

                            {{-- Score & Timer --}}
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium">Max Score</label>
                                    <input type="number" wire:model="max_score" step="0.5" min="0.5"
                                        class="form-control @error('max_score') is-invalid @enderror" />
                                    @error('max_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">
                                        Time Limit
                                        <small class="text-muted fw-normal">(sec)</small>
                                    </label>
                                    <input type="number" wire:model="time_limit" placeholder="e.g. 60"
                                        class="form-control @error('time_limit') is-invalid @enderror" />
                                    @error('time_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-3">

                            {{-- Status --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium">Status</label>
                                <div class="d-flex gap-3">
                                    @foreach (['draft' => ['label' => 'Draft', 'icon' => 'ri-draft-line', 'color' => 'secondary'], 'publish' => ['label' => 'Published', 'icon' => 'ri-global-line', 'color' => 'success'], 'unpublish' => ['label' => 'Unpublished', 'icon' => 'ri-eye-off-line', 'color' => 'warning']] as $val => $opt)
                                        <label
                                            class="status-pill d-flex align-items-center gap-2 px-3 py-2 border rounded-3 cursor-pointer {{ $status === $val ? 'border-' . $opt['color'] . ' bg-label-' . $opt['color'] : '' }}"
                                            style="cursor:pointer">
                                            <input type="radio" wire:model.live="status" value="{{ $val }}"
                                                class="d-none">
                                            <i class="ri {{ $opt['icon'] }} text-{{ $opt['color'] }}"></i>
                                            <span
                                                class="small fw-medium text-{{ $opt['color'] }}">{{ $opt['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Admin Notes --}}
                            <div>
                                <label class="form-label fw-medium">Internal Notes</label>
                                <textarea wire:model="admin_notes" rows="2" class="form-control" placeholder="Notes for content team..."></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── RIGHT COLUMN: Content ────────────────────────── --}}
                <div class="col-lg-8">

                    {{-- Language tabs --}}
                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-transparent border-bottom py-0 px-0">
                            <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-0">
                                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                    <span class="avatar avatar-xs rounded bg-label-info">
                                        <i class="ri ri-translate-2 ri-sm"></i>
                                    </span>
                                    Question Stem & Media
                                </h6>
                                {{-- Language switcher tabs --}}
                                <ul class="nav nav-tabs border-0 gap-1" role="tablist">
                                    @foreach ($languages as $code => $lang)
                                        <li class="nav-item">
                                            <button type="button"
                                                wire:click="$set('activeLang', '{{ $code }}')"
                                                class="nav-link px-3 py-2 small fw-medium d-flex align-items-center gap-1 {{ $activeLang === $code ? 'active' : '' }}">
                                                <span>{{ $lang['flag'] }}</span>
                                                <span>{{ $lang['label'] }}</span>
                                                @if ($code !== 'en')
                                                    <span class="badge bg-label-secondary ms-1"
                                                        style="font-size:10px">Optional</span>
                                                @endif
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            {{-- Language completion indicator --}}
                            <div class="px-4 py-2 bg-light border-top d-flex align-items-center gap-3">
                                @foreach ($languages as $code => $lang)
                                    <div class="d-flex align-items-center gap-1">
                                        <span
                                            class="badge rounded-pill {{ !empty(trim($stem[$code] ?? '')) ? 'bg-success' : 'bg-secondary' }}"
                                            style="width:8px;height:8px;padding:0;"></span>
                                        <span class="small text-muted">{{ $lang['label'] }}</span>
                                    </div>
                                @endforeach
                                <span class="small text-muted ms-auto"><i
                                        class="ri ri-information-line me-1"></i>English is required; others
                                    optional</span>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Stem textarea --}}
                            <div class="mb-3">
                                <label class="form-label fw-medium d-flex align-items-center gap-2">
                                    Question Stem
                                    <span class="badge bg-label-secondary">{{ $languages[$activeLang]['flag'] }}
                                        {{ $languages[$activeLang]['label'] }}</span>
                                    @if ($activeLang === 'en')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <textarea rows="3" class="form-control @error('stem.en') is-invalid @enderror"
                                    placeholder="{{ $activeLang === 'en' ? 'Enter the question in English...' : 'Enter translation (' . $languages[$activeLang]['label'] . ')...' }}"
                                    dir="{{ in_array($activeLang, ['as', 'bn']) ? 'auto' : 'ltr' }}"></textarea>
                                @error('stem.en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Live preview --}}
                            @if (!empty(trim($stem[$activeLang] ?? '')))
                                <div class="question-preview p-3 rounded-3 mb-3"
                                    style="background:var(--bs-light);border-left:3px solid var(--bs-primary);">
                                    <p class="mb-0 fw-medium"
                                        dir="{{ in_array($activeLang, ['as', 'bn']) ? 'auto' : 'ltr' }}"
                                        style="font-size:1.05rem">
                                        <span class="text-muted small me-2">Preview:</span>{{ $stem[$activeLang] }}
                                    </p>
                                </div>
                            @endif

                            {{-- Media attachment --}}
                            <div>
                                <label class="form-label fw-medium">Attach Media <small
                                        class="text-muted fw-normal">(optional)</small></label>
                                <div class="d-flex gap-2 mb-2">
                                    @foreach (['image' => ['icon' => 'ri-image-line', 'label' => 'Image'], 'video' => ['icon' => 'ri-video-line', 'label' => 'Video'], 'audio' => ['icon' => 'ri-music-line', 'label' => 'Audio'], '' => ['icon' => 'ri-close-line', 'label' => 'None']] as $type => $m)
                                        <button type="button" wire:click="$set('mediaType', '{{ $type }}')"
                                            class="btn btn-sm {{ $mediaType === $type ? 'btn-primary' : 'btn-outline-secondary' }} d-flex align-items-center gap-1">
                                            <i class="ri {{ $m['icon'] }}"></i>{{ $m['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                                @if ($mediaType)
                                    <div class="mt-2">
                                        <input type="file" wire:model="mediaUpload" class="form-control"
                                            accept="{{ $mediaType === 'image' ? 'image/*' : ($mediaType === 'video' ? 'video/*' : 'audio/*') }}" />
                                        @if ($mediaPath && !$mediaUpload)
                                            <div class="mt-2 d-flex align-items-center gap-2 text-muted small">
                                                <i class="ri ri-attachment-line"></i>
                                                Current: {{ basename($mediaPath) }}
                                            </div>
                                        @endif
                                        <div wire:loading wire:target="mediaUpload" class="mt-1 text-muted small">
                                            <span class="spinner-border spinner-border-sm me-1"></span>Uploading...
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── OPTIONS ──────────────────────────────────── --}}
                    <div class="card shadow-none border">
                        <div
                            class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs rounded bg-label-success">
                                    <i class="ri ri-list-check ri-sm"></i>
                                </span>
                                Answer Options
                                <span class="badge bg-label-secondary ms-1">MCQ — select one correct</span>
                            </h6>
                            <button type="button" wire:click="addOption"
                                class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                @if (count($options) >= 6) disabled @endif>
                                <i class="ri ri-add-line"></i> Add Option
                            </button>
                        </div>

                        {{-- Validation error for options --}}
                        @error('options')
                            <div class="alert alert-danger d-flex gap-2 m-3 mb-0 py-2">
                                <i class="ri ri-error-warning-line"></i>{{ $message }}
                            </div>
                        @enderror

                        <div class="card-body p-0">

                            @foreach ($options as $i => $option)
                                <div class="option-row {{ $option['is_correct'] ? 'option-correct' : '' }} p-3 {{ !$loop->last ? 'border-bottom' : '' }}"
                                    style="{{ $option['is_correct'] ? 'background:rgba(var(--bs-success-rgb),0.06);border-left:3px solid var(--bs-success)!important;' : '' }}">

                                    <div class="d-flex align-items-start gap-3">

                                        {{-- Option label --}}
                                        <div class="option-letter d-flex align-items-center justify-content-center rounded fw-bold text-white flex-shrink-0"
                                            style="width:34px;height:34px;font-size:14px;background:{{ $option['is_correct'] ? 'var(--bs-success)' : 'var(--bs-secondary)' }}">
                                            {{ $option['id'] }}
                                        </div>

                                        {{-- Text inputs per language --}}
                                        <div class="flex-grow-1">
                                            {{-- Active language input --}}
                                            <div class="mb-2">
                                                <div class="input-group">
                                                    <span
                                                        class="input-group-text bg-transparent border-end-0 text-muted small px-2">
                                                        {{ $languages[$activeLang]['flag'] }}
                                                    </span>
                                                    <input type="text"
                                                        wire:model.live="options.{{ $i }}.text.{{ $activeLang }}"
                                                        class="form-control border-start-0 ps-1 @error('options.' . $i . '.text.en') is-invalid @enderror"
                                                        placeholder="{{ $activeLang === 'en' ? 'Option text in English...' : 'Translation (' . $languages[$activeLang]['label'] . ')...' }}"
                                                        dir="{{ in_array($activeLang, ['as', 'bn']) ? 'auto' : 'ltr' }}" />
                                                    @error('options.' . $i . '.text.en')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Show other language previews if filled --}}
                                            @php
                                                $filledOthers = collect($languages)
                                                    ->keys()
                                                    ->filter(
                                                        fn($c) => $c !== $activeLang &&
                                                            !empty(trim($option['text'][$c] ?? '')),
                                                    );
                                            @endphp
                                            @if ($filledOthers->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($filledOthers as $lc)
                                                        <span class="badge bg-label-secondary fw-normal"
                                                            style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                                            title="{{ $option['text'][$lc] }}">
                                                            {{ $languages[$lc]['flag'] }}
                                                            {{ Str::limit($option['text'][$lc], 30) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Controls --}}
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            {{-- Correct toggle --}}
                                            <div class="d-flex align-items-center">
                                                @if ($option['is_correct'])
                                                    <button type="button"
                                                        wire:click="clearCorrect({{ $i }})"
                                                        class="btn btn-sm btn-success d-flex align-items-center gap-1"
                                                        title="Mark as incorrect">
                                                        <i class="ri ri-checkbox-circle-fill"></i>
                                                        <span class="small">Correct</span>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        wire:click="setCorrect({{ $i }})"
                                                        class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                                        title="Mark as correct">
                                                        <i class="ri ri-checkbox-blank-circle-line"></i>
                                                        <span class="small">Mark Correct</span>
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Remove --}}
                                            <button type="button" wire:click="removeOption({{ $i }})"
                                                class="btn btn-sm btn-icon btn-outline-danger"
                                                @if (count($options) <= 2) disabled @endif
                                                title="Remove option">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                            {{-- Correct answer summary --}}
                            @php $correctIdx = collect($options)->search(fn($o) => $o['is_correct']); @endphp
                            <div class="p-3 bg-light border-top d-flex align-items-center gap-2">
                                @if ($correctIdx !== false)
                                    <span class="badge bg-success d-flex align-items-center gap-1">
                                        <i class="ri ri-checkbox-circle-fill"></i>
                                        Option {{ $options[$correctIdx]['id'] }} is marked correct
                                    </span>
                                    @if (!empty(trim($options[$correctIdx]['text']['en'] ?? '')))
                                        <span class="text-muted small">—
                                            "{{ Str::limit($options[$correctIdx]['text']['en'], 50) }}"</span>
                                    @endif
                                @else
                                    <span class="badge bg-warning d-flex align-items-center gap-1">
                                        <i class="ri ri-alert-line"></i>
                                        No correct answer selected yet
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── EXPLANATION ──────────────────────────────── --}}
                    <div class="card shadow-none border mt-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs rounded bg-label-warning">
                                    <i class="ri ri-lightbulb-line ri-sm"></i>
                                </span>
                                Explanation / Solution
                                <span class="badge bg-label-secondary ms-1">Optional</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <label class="form-label mb-0 fw-medium">
                                    {{ $languages[$activeLang]['flag'] }} {{ $languages[$activeLang]['label'] }}
                                </label>
                            </div>
                            <textarea wire:model.live="explanation.{{ $activeLang }}" rows="2" class="form-control"
                                placeholder="Explain why the correct answer is right (shown after submission)..."
                                dir="{{ in_array($activeLang, ['as', 'bn']) ? 'auto' : 'ltr' }}"></textarea>
                            <div class="form-text text-muted mt-1">
                                <i class="ri ri-information-line me-1"></i>
                                Explanation follows the active language tab — fill all languages for full coverage.
                            </div>
                        </div>
                    </div>

                    {{-- ── BOTTOM ACTION BAR ────────────────────────── --}}
                    <div class="d-flex align-items-center justify-content-between mt-4 p-3 border rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-3 text-muted small">
                            <span><i class="ri ri-translate-2 me-1"></i>
                                {{ collect($languages)->keys()->filter(fn($c) => !empty(trim($stem[$c] ?? '')))->count() }}/{{ count($languages) }}
                                languages filled
                            </span>
                            <span><i class="ri ri-list-check me-1"></i>
                                {{ count($options) }} options
                                ({{ collect($options)->where('is_correct', true)->count() }} correct)
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button wire:click="cancelForm" class="btn btn-outline-secondary">
                                <i class="ri ri-close-line me-1"></i>Cancel
                            </button>
                            <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary">
                                <span wire:loading wire:target="save"
                                    class="spinner-border spinner-border-sm me-2"></span>
                                <i wire:loading.remove wire:target="save" class="ri ri-save-line me-1"></i>
                                {{ $is_edit ? 'Update Question' : 'Save Question' }}
                            </button>
                        </div>
                    </div>

                </div>{{-- /col-lg-8 --}}
            </div>{{-- /row --}}
        </div>
    @endif

    {{-- Loading backdrop --}}
    <div wire:loading>
        @include('utilities.backdrop')
    </div>
</div>
