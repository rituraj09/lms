<?php
namespace App\Livewire\EvaluationMaster;
use App\Helper\Globals;
use App\Helper\Code;
use App\Models\EvaluationMaster\AgeGroup;
use App\Models\EvaluationMaster\DifficultyLevel;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\Question;
use App\Models\EvaluationMaster\QuestionType;
use App\Models\EvaluationMaster\SubSkillType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.backend')]
class QuestionManager extends Component
{
    use WithFileUploads;
    // ─── View State ──────────────────────────────────────────
    // 0 = datatable list, 1 = create/edit form, 3 = preview
    public int $view = 0;
    public bool $isEditing = false;

    // ─── Form: Identity ──────────────────────────────────────
    public ?int $questionId   = null;
    public string $code       = '';
    public string $status     = 'draft';
    public string $pendingStatus = 'draft';

    // ─── Form: Classification ────────────────────────────────
    public ?int $questionTypeId      = null;
    public ?int $primarySkillTypeId  = null;
    public ?int $subSkillTypeId      = null;
    public ?int $difficultyLevelId   = null;
    public ?int $ageGroupId          = null;

    // ─── Form: Scoring ───────────────────────────────────────
    public string $maxScore     = '0.00';
    public float  $negativeMark = 0.0;
    public ?int   $timeLimit    = null;

    // ─── Form: Content ───────────────────────────────────────
    public array  $stem           = [];
    public array  $explanation    = [];
    public array  $options        = [];
    public bool   $isOptionsShuffle = false;
    public string $selectionType  = 'single'; // 'single' | 'multiple'

    // ─── Form: Media ─────────────────────────────────────────
    // Livewire temporary upload object (while editing)
    public mixed $stemImageUpload = null;
    // Persisted path stored in DB (relative to storage/app/public)
    public ?string $stemImagePath = null;
    // Per-option image uploads  [ index => TemporaryUploadedFile|null ]
    public array $optionImageUploads = [];
    // Per-option persisted paths [ index => string|null ]
    public array $optionImagePaths   = [];

    // ─── Form: Admin ─────────────────────────────────────────
    public ?string $adminNotes = null;

    // ─── UI State ────────────────────────────────────────────
    public string $activeLanguageTab = 'en';

    // ─── Reference Data (loaded once in mount) ───────────────
    public array $languages        = [];
    public array $questionTypes    = [];
    public array $primarySkillTypes = [];
    public array $subSkillTypes    = [];
    public array $difficultyLevels = [];
    public array $ageGroups        = [];

    // ─── Option Label Constants ───────────────────────────────
    private const OPTION_LABELS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    private const MAX_OPTIONS   = 8;
    private const MIN_OPTIONS   = 2;

    // =========================================================
    // LIFECYCLE
    // =========================================================

    public function mount(?int $questionId = null): void
    {
        $this->loadReferenceData();
        $this->initializeBlankForm();

        if ($questionId) {
            $this->loadQuestion($questionId);
        }
    }

    // =========================================================
    // VALIDATION
    // =========================================================

    protected function rules(): array
    {
        $uniqueRule = 'unique:questions,code,' . ($this->questionId ?? 'NULL');

        $rules = [
            'code'               => "required|string|max:100|{$uniqueRule}",
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

        foreach (array_keys($this->languages) as $lang) {
            $rules["stem.{$lang}"] = 'nullable|string';
        }

        // Stem image — optional, only validated when a new file is staged
        $rules['stemImageUpload'] = 'nullable|image|mimes:jpeg,png,gif|max:2048';

        foreach ($this->options as $i => $option) {
            $rules["options.{$i}.id"]         = 'required|string';
            $rules["options.{$i}.is_correct"]  = 'boolean';
            $rules["options.{$i}.weightage"]   = 'required|numeric|min:0|max:100';
            $rules["options.{$i}.option_type"] = 'required|in:text,image';
            foreach (array_keys($this->languages) as $lang) {
                $rules["options.{$i}.text.{$lang}"] = 'nullable|string';
            }
            // Per-option image — only validated when present
            $rules["optionImageUploads.{$i}"] = 'nullable|image|mimes:jpeg,png,gif|max:2048';
        }

        return $rules;
    }

    protected array $messages = [
        'code.required'               => 'Question code is required.',
        'questionTypeId.required'     => 'Please select a question type.',
        'primarySkillTypeId.required' => 'Please select a primary skill.',
        'subSkillTypeId.required'     => 'Please select a sub skill.',
        'difficultyLevelId.required'  => 'Please select a difficulty level.',
        'ageGroupId.required'         => 'Please select an age group.',
        'maxScore.required'           => 'Max score is required.',
    ];

    // =========================================================
    // WATCHERS
    // =========================================================

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'options.')) {
            $this->recalculateMaxScore();
        }
    }

    public function updatedSelectionType(): void
    {
        foreach ($this->options as &$opt) {
            $opt['is_correct'] = false;
            $opt['weightage']  = 0;
        }
        $this->recalculateMaxScore();
    }

    // =========================================================
    // COMPUTED PROPERTIES
    // =========================================================

    #[Computed]
    public function correctOptionsCount(): int
    {
        return collect($this->options)->where('is_correct', true)->count();
    }

    // =========================================================
    // OPTION MANAGEMENT
    // =========================================================

    public function addOption(): void
    {
        if (count($this->options) >= self::MAX_OPTIONS) {
            return;
        }

        $index = count($this->options);
        $label = self::OPTION_LABELS[$index] ?? chr(65 + $index);

        $this->options[] = [
            'id'          => $label,
            'option_type' => 'text',           // 'text' | 'image'
            'text'        => array_fill_keys(array_keys($this->languages), ''),
            'weightage'   => 0,
            'is_correct'  => false,
            'image_path'  => null,             // persisted storage path
        ];
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) <= self::MIN_OPTIONS) {
            $this->addError('options', 'Minimum ' . self::MIN_OPTIONS . ' options are required.');
            return;
        }

        // Clean up any staged upload for this slot
        unset($this->optionImageUploads[$index], $this->optionImagePaths[$index]);
        $this->optionImageUploads = array_values($this->optionImageUploads);
        $this->optionImagePaths   = array_values($this->optionImagePaths);

        array_splice($this->options, $index, 1);
        $this->reindexOptionLabels();
        $this->recalculateMaxScore();
    }

    public function toggleCorrect(int $index): void
    {
        if ($this->selectionType === 'single') {
            foreach ($this->options as $i => &$opt) {
                $opt['is_correct'] = ($i === $index);
            }
        } else {
            $this->options[$index]['is_correct'] = ! $this->options[$index]['is_correct'];
        }

        $this->recalculateMaxScore();
    }

    // =========================================================
    // MEDIA MANAGEMENT
    // =========================================================

    /** Called by wire:model on the stem image input. */
    public function updatedStemImageUpload(): void
    {
        $this->validateOnly('stemImageUpload');
    }

    /** Remove the staged stem image (before save). */
    public function removeStemImageUpload(): void
    {
        $this->stemImageUpload = null;
        $this->resetErrorBag('stemImageUpload');
    }

    /** Remove the already-persisted stem image (on edit). */
    public function removeStemImagePath(): void
    {
        if ($this->stemImagePath) {
            Storage::disk('public')->delete($this->stemImagePath);
        }
        $this->stemImagePath = null;
    }

    /** Called by wire:model on a per-option image input. */
    public function updatedOptionImageUploads(mixed $value, string $index): void
    {
        $this->validateOnly("optionImageUploads.{$index}");
    }

    /** Remove a staged option image (before save). */
    public function removeOptionImageUpload(int $index): void
    {
        unset($this->optionImageUploads[$index]);
        $this->resetErrorBag("optionImageUploads.{$index}");
    }

    /** Remove a persisted option image (on edit). */
    public function removeOptionImagePath(int $index): void
    {
        $path = $this->options[$index]['image_path'] ?? null;
        if ($path) {
            Storage::disk('public')->delete($path);
        }
        $this->options[$index]['image_path'] = null;
        unset($this->optionImagePaths[$index]);
    }

    /** Toggle option_type between text and image; clear the other side. */
    public function setOptionType(int $index, string $type): void
    {
        $this->options[$index]['option_type'] = $type;

        if ($type === 'text') {
            // Clear any staged or persisted image for this option
            unset($this->optionImageUploads[$index]);
            $this->options[$index]['image_path'] = null;
        } else {
            // Clear text when switching to image
            foreach (array_keys($this->languages) as $lang) {
                $this->options[$index]['text'][$lang] = '';
            }
        }
    }

    // =========================================================
    // VIEW NAVIGATION
    // =========================================================

    public function showPreview(): void
    {
        $this->validate();
        $this->view = 3;
    }

    public function backToForm(): void
    {
        $this->view = 1;
    }

    #[On('new-entry')]
    public function newEntry(): void
    {
        $this->resetForm();
        $this->initializeBlankForm();
        $this->view = 1;
    }

    public function resetForm(): void
    {
        $this->reset([
            'questionId', 'code', 'status', 'pendingStatus',
            'questionTypeId', 'primarySkillTypeId', 'subSkillTypeId',
            'difficultyLevelId', 'ageGroupId',
            'maxScore', 'negativeMark', 'timeLimit',
            'stem', 'explanation', 'options',
            'isOptionsShuffle', 'selectionType',
            'adminNotes', 'activeLanguageTab',
            'stemImageUpload', 'stemImagePath',
            'optionImageUploads', 'optionImagePaths',
        ]);

        $this->isEditing = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // =========================================================
    // PERSISTENCE
    // =========================================================

    public function save(): void
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // ── Stem image ────────────────────────────────────
            if ($this->stemImageUpload) {
                // Delete old file if replacing
                if ($this->stemImagePath) {
                    Storage::disk('public')->delete($this->stemImagePath);
                }
                $this->stemImagePath = $this->stemImageUpload
                    ->store('questions/stems', 'public');
                $this->stemImageUpload = null;
            }

            // ── Option images ─────────────────────────────────
            foreach ($this->optionImageUploads as $i => $upload) {
                if (! $upload) continue;

                // Delete old option image if replacing
                $oldPath = $this->options[$i]['image_path'] ?? null;
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }

                $this->options[$i]['image_path'] = $upload
                    ->store('questions/options', 'public');
            }
            $this->optionImageUploads = [];

            $contents = $this->buildQuestionContents();

            Question::updateOrCreate(
                ['id' => $this->questionId],
                [
                    'code'                  => $this->code,
                    'question_type_id'      => $this->questionTypeId,
                    'primary_skill_type_id' => $this->primarySkillTypeId,
                    'sub_skill_type_id'     => $this->subSkillTypeId,
                    'difficulty_level_id'   => $this->difficultyLevelId,
                    'age_group_id'          => $this->ageGroupId,
                    'question_contents'     => json_encode($contents),
                    'time_limit'            => $this->timeLimit,
                    'max_score'             => $this->maxScore,
                    'admin_notes'           => $this->adminNotes,
                    'status'                => $this->pendingStatus ?: $this->status,
                ]
            );

            DB::commit();

            session()->flash('success', 'Question saved successfully.');
            $this->resetForm();
            $this->view = 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('QuestionManager: save failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            $this->addError('save', 'Failed to save question: ' . $e->getMessage());
        }
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        return view('livewire.evaluation-master.question-manager');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function loadReferenceData(): void
    {
        $this->languages        = Globals::LANGUAGES;
        $this->questionTypes    = QuestionType::select('id', 'name')->get()->toArray();
        $this->primarySkillTypes = PrimarySkillType::select('id', 'name')->get()->toArray();
        $this->subSkillTypes    = SubSkillType::select('id', 'name')->get()->toArray();
        $this->difficultyLevels = DifficultyLevel::select('id', 'name')->get()->toArray();
        $this->ageGroups        = AgeGroup::select('id', 'name')->get()->toArray();
    }

    private function initializeBlankForm(): void
    {
       $this->code   = Code::generateQuestionCode(Auth::guard('admin')->id());
        // $this->code   = 'Q-' . strtoupper(Str::random(8));
        $this->status = 'draft';
        $this->pendingStatus = 'draft';

        $langKeys = array_keys($this->languages);
        $this->stem        = array_fill_keys($langKeys, '');
        $this->explanation = array_fill_keys($langKeys, '');
        $this->options     = [];

        $this->addOption();
        $this->addOption();
    }

    private function loadQuestion(int $id): void
    {
        $question = Question::findOrFail($id);

        $this->questionId          = $question->id;
        $this->code                = $question->code;
        $this->questionTypeId      = $question->question_type_id;
        $this->primarySkillTypeId  = $question->primary_skill_type_id;
        $this->subSkillTypeId      = $question->sub_skill_type_id;
        $this->difficultyLevelId   = $question->difficulty_level_id;
        $this->ageGroupId          = $question->age_group_id;
        $this->timeLimit           = $question->time_limit;
        $this->maxScore            = (string) $question->max_score;
        $this->adminNotes          = $question->admin_notes;
        $this->status              = $question->status;
        $this->isEditing           = true;
        $this->view                = 1;

        $contents = json_decode($question->question_contents, true) ?? [];

        $this->stem             = $contents['stem']               ?? array_fill_keys(array_keys($this->languages), '');
        $this->stemImagePath    = $contents['stem_image_path']    ?? null;
        $this->explanation      = $contents['explanation']         ?? array_fill_keys(array_keys($this->languages), '');
        $this->options          = $contents['options']             ?? [];
        $this->negativeMark     = (float) ($contents['negative_mark']      ?? 0);
        $this->isOptionsShuffle = (bool)  ($contents['is_options_shuffle'] ?? false);
        $this->selectionType    = $contents['selection_type']     ?? 'single';

        // Ensure every loaded option has the option_type key (backwards compat)
        foreach ($this->options as &$opt) {
            $opt['option_type'] ??= 'text';
            $opt['image_path']  ??= null;
        }
    }

    private function buildQuestionContents(): array
    {
        return [
            'stem'               => $this->stem,
            'stem_image_path'    => $this->stemImagePath,
            'explanation'        => $this->explanation,
            'options'            => $this->options,  // each option carries its own image_path
            'negative_mark'      => $this->negativeMark,
            'is_options_shuffle' => $this->isOptionsShuffle,
            'selection_type'     => $this->selectionType,
        ];
    }

    private function reindexOptionLabels(): void
    {
        foreach ($this->options as $i => &$option) {
            $option['id'] = self::OPTION_LABELS[$i] ?? chr(65 + $i);
        }
    }

    private function recalculateMaxScore(): void
    {
        $this->maxScore = (string) collect($this->options)
            ->where('is_correct', true)
            ->sum(fn ($opt) => (float) ($opt['weightage'] ?? 0));
    }
}
