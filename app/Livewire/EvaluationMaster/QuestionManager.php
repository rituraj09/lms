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
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.backend')]
class QuestionManager extends Component
{
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
            $rules["stem.{$lang}"] = 'nullable|string|max:99999';
        }

        foreach ($this->options as $i => $option) {
            $rules["options.{$i}.id"]         = 'required|string';
            $rules["options.{$i}.is_correct"]  = 'boolean';
            $rules["options.{$i}.weightage"]   = 'required|numeric|min:0|max:100';
            foreach (array_keys($this->languages) as $lang) {
                $rules["options.{$i}.text.{$lang}"] = 'nullable|string';
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
        'ageGroupId.required'         => 'Please select an age group.',
        'maxScore.required'           => 'Max score is required.',
        'stem.required'               => 'Question stem cannot be blank.',
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
            'id'         => $label,
            'text'       => array_fill_keys(array_keys($this->languages), ''),
            'weightage'  => 0,
            'is_correct' => false,
        ];
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) <= self::MIN_OPTIONS) {
            $this->addError('options', 'Minimum ' . self::MIN_OPTIONS . ' options are required.');
            return;
        }

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

        $this->stem             = $contents['stem']             ?? array_fill_keys(array_keys($this->languages), '');
        $this->explanation      = $contents['explanation']      ?? array_fill_keys(array_keys($this->languages), '');
        $this->options          = $contents['options']          ?? [];
        $this->negativeMark     = (float) ($contents['negative_mark']    ?? 0);
        $this->isOptionsShuffle = (bool)  ($contents['is_options_shuffle'] ?? false);
        $this->selectionType    = $contents['selection_type']   ?? 'single';
    }

    private function buildQuestionContents(): array
    {
        return [
            'stem'               => $this->stem,
            'explanation'        => $this->explanation,
            'options'            => $this->options,
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
