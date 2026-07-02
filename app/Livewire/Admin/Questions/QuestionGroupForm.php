<?php
namespace App\Livewire\Admin\Questions;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helper\Globals;
use App\Models\QuestionMaster\QuestionGroup;
use App\Models\QuestionMaster\Question;
use App\Models\EvaluationMaster\PrimarySkillType;
use App\Models\EvaluationMaster\SubSkillType;
use App\Models\EvaluationMaster\DifficultyLevel;
use App\Models\EvaluationMaster\AgeGroup;

#[Layout('layouts.backend')]
class QuestionGroupForm extends Component
{
    use WithFileUploads;

    /* ================================================================
     | VIEW STATES
     | 'group_form'     → Create / Edit group info
     | 'group_view'     → Group saved — show readonly group + questions list
     | 'question_form'  → Add / Edit single question
     * ================================================================*/
    public string $view = 'group_form';

    /* ================================================================
     | LOCKED
     * ================================================================*/
    #[Locked]
    public ?int $groupId = null;

    #[Locked]
    public bool $isGroupLocked = false;

    /* ================================================================
     | GROUP FIELDS
     * ================================================================*/
    public string $group_code        = '';
    public string $questions_category = 'single';
    public string $admin_note        = '';

    /**
     * group_content JSON structure:
     * {
     *   "title": {
     *     "en": "...",
     *     "hn": "...",
     *     ...
     *   },
     *   "content": {
     *     "en": "<p>Rich text...</p>",
     *     "hn": "...",
     *     ...
     *   },
     *   "image": "question-groups/images/xxx.jpg"  ← NEW (only for 'multiple')
     * }
     *
     * "content" key is only used when questions_category === 'multiple'
     */
    public array $group_content = [];

    /* ================================================================
     | GROUP CONTENT IMAGE UPLOAD  ← NEW
     * ================================================================*/
    public $groupContentImage                = null;   // TemporaryUploadedFile
    public ?string $existingGroupContentImage = null;  // already-saved path

    /* ================================================================
     | ACTIVE QUESTION
     * ================================================================*/
    public array $activeQuestion = [];

    /* ================================================================
     | QUESTION IMAGE UPLOADS
     * ================================================================*/
    public $stemImageUpload  = null;
    public array $optionImages = [];

    /* ================================================================
     | QUESTIONS LIST
     * ================================================================*/
    public array $questionsList = [];

    /* ================================================================
     | DROPDOWN DATA
     * ================================================================*/
    public array $primarySkillTypes = [];
    public array $subSkillTypes     = [];
    public array $difficultyLevels  = [];
    public array $ageGroups         = [];
    /* ================================================================
     | INTERNAL
     * ================================================================*/
    public array $languages = [];

    public bool  $showQuestionPreview = false;
    public array $previewQuestion     = [];

    /* ================================================================
     | MOUNT
     * ================================================================*/
    public function mount(?int $groupId = null): void
    {
        $this->languages = array_keys(Globals::LANGUAGES);
        $this->groupId   = $groupId;

        $this->loadDropdowns();
        $this->initGroupContent();

        if ($groupId) {
            $this->loadGroup($groupId);
            $this->loadQuestionsList();
            $this->view = 'group_view';
        } else {
            $this->group_code = $this->generateGroupCode();
            $this->view       = 'group_form';
        }
    }

    /* ================================================================
     | DROPDOWNS
     * ================================================================*/
    private function loadDropdowns(): void
    {
        $this->primarySkillTypes = PrimarySkillType::orderBy('name')
            ->get(['id', 'name'])->toArray();

        $this->subSkillTypes = SubSkillType::orderBy('name')
            ->get(['id', 'name'])->toArray();

        $this->difficultyLevels = DifficultyLevel::orderBy('name')
            ->get(['id', 'name'])->toArray();

        $this->ageGroups = AgeGroup::orderBy('name')
            ->get(['id', 'name'])->toArray();
    }

    /* ================================================================
     | INITIALIZERS
     * ================================================================*/
    /**
     * Ensure all language keys exist in group_content
     * for both 'title' and 'content' nodes.
     */
    private function initGroupContent(): void
    {
        foreach ($this->languages as $lang) {
            $this->group_content['title'][$lang]   ??= '';
            $this->group_content['content'][$lang] ??= '';
        }
        // Ensure image key exists
        $this->group_content['image'] ??= null;
    }

    private function emptyQuestion(): array
    {
        $stem        = [];
        $explanation = [];

        foreach ($this->languages as $lang) {
            $stem[$lang]        = '';
            $explanation[$lang] = '';
        }

        return [
            'id'                  => null,
            'question_code'       => $this->generateQuestionCode(),
            'answer_category'     => 'single_choice',
            'marks'               => 1,
            'primary_skill_id'    => null,
            'sub_skill_id'        => null,
            'difficulty_level_id' => null,
            'age_group_id'        => null,
            'stem'                => $stem,
            'existing_image'      => null,
            'options'             => [
                $this->emptyOption(),
                $this->emptyOption(),
            ],
            'explanation'         => $explanation,
        ];
    }

    private function emptyOption(): array
    {
        $text = [];
        foreach ($this->languages as $lang) {
            $text[$lang] = '';
        }

        return [
            'option_type' => 'text',
            'is_correct'  => false,
            'weightage'   => 0,
            'text'        => $text,
            'image_path'  => null,
        ];
    }

    /* ================================================================
     | CODE GENERATORS
     * ================================================================*/
    private function generateGroupCode(): string
    {
        do {
            $code = 'GRP-' . strtoupper(Str::random(8));
        } while (QuestionGroup::where('group_code', $code)->exists());

        return $code;
    }

    private function generateQuestionCode(): string
    {
        do {
            $code = 'QUE-' . strtoupper(Str::random(8));
        } while (Question::where('question_code', $code)->exists());

        return $code;
    }

    /* ================================================================
     | LOAD GROUP
     * ================================================================*/
    private function loadGroup(int $id): void
    {
        $group = QuestionGroup::findOrFail($id);

        $this->isGroupLocked      = $group->isLinkedToAssessment();
        $this->group_code         = $group->group_code;
        $this->questions_category = $group->questions_category;
        $this->admin_note         = $group->admin_note ?? '';

        $stored = $group->group_content ?? [];

        foreach ($this->languages as $lang) {
            // Title
            $this->group_content['title'][$lang]
                = $stored['title'][$lang] ?? '';

            // Content (multilanguage Quill — only relevant for 'multiple' category)
            $this->group_content['content'][$lang]
                = $stored['content'][$lang] ?? '';
        }

        // Load stored image  ← NEW
        $this->group_content['image']      = $stored['image'] ?? null;
        $this->existingGroupContentImage   = $stored['image'] ?? null;
    }

    /* ================================================================
     | LOAD QUESTIONS LIST
     * ================================================================*/
    public function loadQuestionsList(): void
    {
        if (! $this->groupId) {
            return;
        }

        $this->questionsList = Question::where('question_group_id', $this->groupId)
            ->with(['primarySkill', 'subSkill', 'difficultyLevel', 'ageGroup'])
            ->latest()
            ->get()
            ->map(function ($q) {
                $content = $q->question_content ?? [];

                return [
                    'id'             => $q->id,
                    'question_code'  => $q->question_code,
                    'answer_category'=> $q->answer_category,
                    'marks'          => $content['marks'] ?? 0,
                    'primary_skill'  => $q->primarySkill?->name  ?? '—',
                    'difficulty'     => $q->difficultyLevel?->name ?? '—',
                    'age_group'      => $q->ageGroup?->name       ?? '—',
                    'stem_en'        => strip_tags($content['stem']['en'] ?? ''),
                    'options_count'  => count($content['options'] ?? []),
                    'in_assessment'  => $q->isUsedInAssessment(),
                ];
            })
            ->toArray();
    }

    /* ================================================================
     | UPDATED HOOK — questions_category change
     | When category switches, ensure content keys exist
     * ================================================================*/
    public function updatedQuestionsCategory(): void
    {
        // Always ensure content keys are initialised
        foreach ($this->languages as $lang) {
            $this->group_content['content'][$lang] ??= '';
        }
        // Ensure image key exists
        $this->group_content['image'] ??= null;
    }

    /* ================================================================
     | GROUP CONTENT IMAGE HELPERS  ← NEW
     * ================================================================*/

    /**
     * Remove the already-saved image from disk and clear state.
     */
    public function removeGroupContentImagePath(): void
    {
        $path = $this->existingGroupContentImage;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $this->existingGroupContentImage = null;
        $this->group_content['image']    = null;
    }

    /**
     * Discard a newly-selected upload (not yet saved).
     */
    public function removeGroupContentImageUpload(): void
    {
        $this->groupContentImage = null;
    }

    /* ================================================================
     | GROUP FORM ACTIONS
     * ================================================================*/
    public function editGroup(): void
    {
        $this->view = 'group_form';
    }

    public function saveGroup(): void
    {
        if ($this->isGroupLocked) {
            $this->addError(
                'locked',
                'This group is linked to an assessment and cannot be modified.'
            );
            return;
        }

        $rules = [
            'group_code'        => 'required|string|max:100',
            'questions_category'=> 'required|in:single,multiple',
            'group_content'     => 'nullable|array',
            'groupContentImage' => 'nullable|image|max:5120',   // ← NEW
        ];

        // If multiple, require at least English content
        if ($this->questions_category === 'multiple') {
            $rules['group_content.content.en'] = 'required|string|min:3';
        }

        $messages = [
            'group_code.required'               => 'Group code is required.',
            'questions_category.required'       => 'Please select a question category.',
            'group_content.content.en.required' => 'Please enter the group content in English.',
            'group_content.content.en.min'      => 'Group content is too short.',
            'groupContentImage.image'           => 'File must be a valid image.',
            'groupContentImage.max'             => 'Image must not exceed 5 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            // ── Handle group content image upload  ← NEW ─────────────
            $imagePath = $this->existingGroupContentImage;

            if ($this->groupContentImage) {
                // Delete old image if it exists
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $this->groupContentImage
                    ->store('question-groups/images', 'public');

                $this->existingGroupContentImage = $imagePath;
                $this->groupContentImage         = null;
            }

            // Embed image path into the JSON payload
            $this->group_content['image'] = $imagePath;
            // ─────────────────────────────────────────────────────────

            $group = QuestionGroup::updateOrCreate(
                ['id' => $this->groupId],
                [
                    'group_code'         => $this->group_code,
                    'questions_category' => $this->questions_category,
                    'group_content'      => $this->group_content,
                    'admin_note'         => $this->admin_note,
                    'created_by'         => $this->groupId
                        ? QuestionGroup::find($this->groupId)?->created_by
                        : auth()->id(),
                    'updated_by'         => auth()->id(),
                ]
            );

            $this->groupId = $group->id;
            $this->loadQuestionsList();
            $this->view = 'group_view';

            session()->flash('success', 'Question Group saved successfully.');

        } catch (\Throwable $e) {
            Log::error('saveGroup failed', ['error' => $e->getMessage()]);
            $this->addError('save_failed', 'Something went wrong. Please try again.');
        }
    }

    public function cancelGroupEdit(): void
    {
        if ($this->groupId) {
            $this->loadGroup($this->groupId);
            $this->view = 'group_view';
        }
    }

    /* ================================================================
     | QUESTION FORM ACTIONS
     * ================================================================*/
    public function addNewQuestion(): void
    {
        $this->resetQuestionForm();
        $this->activeQuestion = $this->emptyQuestion();
        $this->view           = 'question_form';
    }

    /* ================================================================
     | EDIT QUESTION — Load existing question into activeQuestion
     * ================================================================*/
    public string $questionFormKey = '';

    public function editQuestion(int $questionId): void
    {
        $question = Question::with([
            'primarySkill',
            'subSkill',
            'difficultyLevel',
            'ageGroup',
        ])->findOrFail($questionId);

        if ($question->isUsedInAssessment()) {
            session()->flash(
                'error',
                'This question is used in an assessment and cannot be edited.'
            );
            return;
        }

        $content = $question->question_content ?? [];

        /* ── Stem ─────────────────────────────────────────────────── */
        $stem = [];
        foreach ($this->languages as $lang) {
            $stem[$lang] = $content['stem'][$lang] ?? '';
        }

        /* ── Explanation ──────────────────────────────────────────── */
        $explanation = [];
        foreach ($this->languages as $lang) {
            // Also try top-level 'explanation' key or nested
            $explanation[$lang] = $content['explanation'][$lang] ?? '';
        }

        /* ── Options ──────────────────────────────────────────────── */
        $options    = [];
        $rawOptions = $content['options'] ?? [];

        // Pad to minimum 2 options
        while (count($rawOptions) < 2) {
            $rawOptions[] = [];
        }

        foreach ($rawOptions as $opt) {
            $text = [];
            foreach ($this->languages as $lang) {
                $text[$lang] = $opt['text'][$lang] ?? '';
            }

            $options[] = [
                'option_type' => $opt['option_type'] ?? 'text',
                'is_correct'  => (bool) ($opt['is_correct'] ?? false),
                'weightage'   => (float) ($opt['weightage'] ?? 0),
                'text'        => $text,
                'image_path'  => $opt['image_path'] ?? null,
            ];
        }

        /* ── Set activeQuestion ───────────────────────────────────── */
        $this->activeQuestion = [
            'id'                  => $question->id,
            'question_code'       => $question->question_code,
            'answer_category'     => $question->answer_category,
            'marks'               => (float) ($content['marks'] ?? 1),
            'primary_skill_id'    => $question->primary_skill_id,
            'sub_skill_id'        => $question->sub_skill_id,
            'difficulty_level_id' => $question->difficulty_level_id,
            'age_group_id'        => $question->age_group_id,
            'stem'                => $stem,
            'existing_image'      => $content['image'] ?? null,
            'options'             => $options,
            'explanation'         => $explanation,
        ];

        /* ── Force full re-render of question form ────────────────── */
        $this->questionFormKey = 'edit-' . $questionId . '-' . time();
        $this->stemImageUpload = null;
        $this->optionImages    = [];
        $this->resetErrorBag();
        $this->view = 'question_form';
    }

    public function saveQuestion(): void
    {
        $this->validateQuestion();

        $category = $this->activeQuestion['answer_category'] ?? 'single_choice';

        if ($category !== 'open_text') {
            $correctCount = collect($this->activeQuestion['options'])
                ->where('is_correct', true)->count();

            if ($correctCount === 0) {
                $this->addError(
                    'activeQuestion.options',
                    'Please mark at least one correct answer.'
                );
                return;
            }
        }

        try {
            DB::transaction(function () {
                $stemImagePath = $this->activeQuestion['existing_image'] ?? null;

                if ($this->stemImageUpload) {
                    if ($stemImagePath
                        && Storage::disk('public')->exists($stemImagePath)) {
                        Storage::disk('public')->delete($stemImagePath);
                    }
                    $stemImagePath = $this->stemImageUpload
                        ->store("questions/{$this->groupId}/stems", 'public');

                    $this->activeQuestion['existing_image'] = $stemImagePath;
                    $this->stemImageUpload                  = null;
                }

                $options = $this->activeQuestion['options'];

                foreach ($options as $j => $opt) {
                    if (! empty($this->optionImages[$j])) {
                        if (! empty($opt['image_path'])
                            && Storage::disk('public')->exists($opt['image_path'])) {
                            Storage::disk('public')->delete($opt['image_path']);
                        }

                        $optPath = $this->optionImages[$j]
                            ->store("questions/{$this->groupId}/options", 'public');

                        $options[$j]['image_path']                           = $optPath;
                        $this->activeQuestion['options'][$j]['image_path']   = $optPath;
                        unset($this->optionImages[$j]);
                    }
                }

                $questionContent = [
                    'stem'        => $this->activeQuestion['stem'],
                    'image'       => $stemImagePath,
                    'options'     => $options,
                    'marks'       => $this->activeQuestion['marks'],
                    'explanation' => $this->activeQuestion['explanation'],
                ];

                $question = Question::updateOrCreate(
                    ['id' => $this->activeQuestion['id'] ?? null],
                    [
                        'question_group_id'   => $this->groupId,
                        'question_code'       => $this->activeQuestion['question_code'],
                        'primary_skill_id'    => $this->activeQuestion['primary_skill_id'],
                        'sub_skill_id'        => $this->activeQuestion['sub_skill_id'],
                        'difficulty_level_id' => $this->activeQuestion['difficulty_level_id'],
                        'age_group_id'        => $this->activeQuestion['age_group_id'],
                        'answer_category'     => $this->activeQuestion['answer_category'],
                        'question_content'    => $questionContent,
                        'explaination'        => json_encode($this->activeQuestion['explanation']),
                        'admin_notes'         => null,
                        'created_by'          => $this->activeQuestion['id']
                            ? Question::find($this->activeQuestion['id'])?->created_by
                            : auth()->id(),
                        'updated_by'          => auth()->id(),
                    ]
                );

                $this->activeQuestion['id'] = $question->id;
            });

            $this->loadQuestionsList();
            $this->resetQuestionForm();
            $this->view = 'group_view';

            session()->flash('success', 'Question saved successfully.');

        } catch (\Throwable $e) {
            Log::error('saveQuestion failed', ['error' => $e->getMessage()]);
            $this->addError('save_failed', 'Something went wrong. Please try again.');
        }
    }

    public function cancelQuestion(): void
    {
        $this->resetQuestionForm();
        $this->view = 'group_view';
    }

    public function deleteQuestion(int $questionId): void
    {
        $question = Question::find($questionId);

        if (! $question) return;

        if ($question->isUsedInAssessment()) {
            session()->flash(
                'error',
                'This question is used in an assessment and cannot be deleted.'
            );
            return;
        }

        $content = $question->question_content ?? [];

        if (! empty($content['image'])) {
            Storage::disk('public')->delete($content['image']);
        }

        foreach ($content['options'] ?? [] as $opt) {
            if (! empty($opt['image_path'])) {
                Storage::disk('public')->delete($opt['image_path']);
            }
        }

        $question->forceDelete();
        $this->loadQuestionsList();

        session()->flash('success', 'Question deleted successfully.');
    }

    /* ================================================================
     | RESET QUESTION FORM
     * ================================================================*/
    private function resetQuestionForm(): void
    {
        $this->activeQuestion  = [];
        $this->stemImageUpload = null;
        $this->optionImages    = [];
        $this->resetErrorBag();
    }

    /* ================================================================
     | OPTION MANAGEMENT
     * ================================================================*/
    public function addOption(): void
    {
        if (count($this->activeQuestion['options'] ?? []) >= 8) return;

        $this->activeQuestion['options'][] = $this->emptyOption();
    }

    public function removeOption(int $optIndex): void
    {
        if (count($this->activeQuestion['options']) <= 2) {
            $this->addError('options_min', 'Minimum 2 options are required.');
            return;
        }

        $imagePath = $this->activeQuestion['options'][$optIndex]['image_path'] ?? null;

        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        unset($this->activeQuestion['options'][$optIndex]);
        $this->activeQuestion['options'] = array_values($this->activeQuestion['options']);

        unset($this->optionImages[$optIndex]);
        $this->optionImages = array_values($this->optionImages);
    }

    /**
     * Auto-calculate marks from total weightage
     * whenever any option changes (weightage / is_correct).
     */
    /**
     * Fired when ANY nested key inside activeQuestion changes.
     */

    public function updateWeightage(int $optIndex, float $value): void
    {
        $this->activeQuestion['options'][$optIndex]['weightage'] = $value;
        $this->recalculateMarksFromWeightage();
    }

    private function recalculateMarksFromWeightage(): void
    {
        // Auto-calculate for both single_choice and multi_choice
        $total = collect($this->activeQuestion['options'] ?? [])
            ->where('is_correct', true)
            ->sum(fn($opt) => (float) ($opt['weightage'] ?? 0));

        $this->activeQuestion['marks'] = $total;
    }
    public function toggleCorrect(int $optIndex): void
    {
        $category = $this->activeQuestion['answer_category'] ?? 'single_choice';

        if ($category === 'single_choice') {
            foreach ($this->activeQuestion['options'] as $i => $_) {
                $this->activeQuestion['options'][$i]['is_correct'] = ($i === $optIndex);
                if ($i !== $optIndex) {
                    $this->activeQuestion['options'][$i]['weightage'] = 0;
                }
            }
        } else {
            $current = $this->activeQuestion['options'][$optIndex]['is_correct'] ?? false;
            $this->activeQuestion['options'][$optIndex]['is_correct'] = ! $current;

            if ($current) {
                $this->activeQuestion['options'][$optIndex]['weightage'] = 0;
            }
        }

        // Recalculate marks for both single_choice and multi_choice
        if (in_array($category, ['single_choice', 'multi_choice'])) {
            $this->recalculateMarksFromWeightage();
        }
    }

    public function setOptionType(int $optIndex, string $type): void
    {
        $this->activeQuestion['options'][$optIndex]['option_type'] = $type;

        if ($type === 'text') {
            $path = $this->activeQuestion['options'][$optIndex]['image_path'] ?? null;

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $this->activeQuestion['options'][$optIndex]['image_path'] = null;
            unset($this->optionImages[$optIndex]);
        }
    }

    /* ================================================================
     | STEM IMAGE
     * ================================================================*/
    public function removeStemImagePath(): void
    {
        $path = $this->activeQuestion['existing_image'] ?? null;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $this->activeQuestion['existing_image'] = null;
    }

    public function removeStemImageUpload(): void
    {
        $this->stemImageUpload = null;
    }

    /* ================================================================
     | OPTION IMAGE
     * ================================================================*/
    public function removeOptionImagePath(int $optIndex): void
    {
        $path = $this->activeQuestion['options'][$optIndex]['image_path'] ?? null;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $this->activeQuestion['options'][$optIndex]['image_path'] = null;
    }

    public function removeOptionImageUpload(int $optIndex): void
    {
        unset($this->optionImages[$optIndex]);
    }

    /* ================================================================
     | QUESTION VALIDATION
     * ================================================================*/
    private function validateQuestion(): void
    {
        $rules = [
            'activeQuestion.answer_category'     =>
                'required|in:single_choice,multi_choice,open_text',
            'activeQuestion.marks'               => 'required|numeric|min:0',
            'activeQuestion.primary_skill_id'    => 'required|integer',
            'activeQuestion.sub_skill_id'        => 'required|integer',
            'activeQuestion.difficulty_level_id' => 'required|integer',
            'activeQuestion.age_group_id'        => 'required|integer',
            'activeQuestion.stem.en'             => 'required|string|min:3',
            'stemImageUpload'                    => 'nullable|image|max:2048',
        ];

        $category = $this->activeQuestion['answer_category'] ?? 'single_choice';

        if ($category !== 'open_text') {
            $rules['activeQuestion.options'] = 'required|array|min:2';

            foreach ($this->activeQuestion['options'] ?? [] as $j => $opt) {
                if (($opt['option_type'] ?? 'text') === 'text') {
                    $rules["activeQuestion.options.{$j}.text.en"] = 'required|string|min:1';
                } else {
                    $rules["optionImages.{$j}"] = empty($opt['image_path'])
                        ? 'required|image|max:2048'
                        : 'nullable|image|max:2048';
                }
            }
        }

        $this->validate($rules, [
            'activeQuestion.answer_category.required'     => 'Answer category is required.',
            'activeQuestion.marks.required'               => 'Marks are required.',
            'activeQuestion.marks.min'                    => 'Marks cannot be negative.',
            'activeQuestion.primary_skill_id.required'    => 'Please select a primary skill.',
            'activeQuestion.sub_skill_id.required'        => 'Please select a sub skill.',
            'activeQuestion.difficulty_level_id.required' => 'Please select a difficulty level.',
            'activeQuestion.age_group_id.required'        => 'Please select an age group.',
            'activeQuestion.stem.en.required'             => 'English question stem is required.',
            'activeQuestion.stem.en.min'                  => 'Question stem is too short.',
            'activeQuestion.options.required'             => 'Please add at least 2 options.',
            'activeQuestion.options.min'                  => 'Please add at least 2 options.',
            'stemImageUpload.image'                       => 'File must be a valid image.',
            'stemImageUpload.max'                         => 'Image must not exceed 2 MB.',
        ]);
    }
    public function viewQuestion(int $questionId): void
    {
        $question = Question::with('questionGroup')->findOrFail($questionId);

        $raw    = $question->getRawOriginal('question_content');
        $content = is_array($question->question_content)
            ? $question->question_content
            : (is_string($raw) ? json_decode($raw, true) : []);

        if (empty($content)) {
            $this->previewQuestion     = [];
            $this->showQuestionPreview = true;
            return;
        }

        $stems = [];
        foreach ($this->languages as $lang) {
            $stemHtml = $content['stem'][$lang] ?? '';
            if (!empty(trim(strip_tags($stemHtml)))) {
                $stems[$lang] = $stemHtml;
            }
        }

        $marks = $content['marks'] ?? 0;

        $passage = null;
        if ($question->questionGroup &&
            $question->questionGroup->questions_category === 'multiple') {

            $rawGroup     = $question->questionGroup->getAttributes()['group_content'];
            $groupContent = is_array($question->questionGroup->group_content)
                ? $question->questionGroup->group_content
                : json_decode($rawGroup, true);

            $passageData = [];
            foreach ($this->languages as $lang) {
                $passageText = $groupContent['content'][$lang] ?? '';
                if (!empty(trim(strip_tags($passageText)))) {
                    $passageData[$lang] = $passageText;
                }
            }
            if (!empty($passageData)) {
                $passage = $passageData;
            }
        }

        $formattedOptions = [];
        $rawOptions       = $content['options'] ?? [];

        foreach ($rawOptions as $index => $opt) {
            $optionTexts = [];
            foreach ($this->languages as $lang) {
                $optText = $opt['text'][$lang] ?? '';
                if (!empty(trim($optText))) {
                    $optionTexts[$lang] = $optText;
                }
            }

            $formattedOptions[] = [
                'index'      => $index + 1,
                'texts'      => $optionTexts,
                'is_correct' => (bool) ($opt['is_correct'] ?? false),
                'weightage'  => $opt['weightage'] ?? 0,
                'type'       => $opt['option_type'] ?? 'text',
            ];
        }

        $this->previewQuestion = [
            'id'              => $question->id,
            'code'            => $question->question_code,
            'stems'           => $stems,
            'passage'         => $passage,
            'answer_category' => $question->answer_category ?? $content['answer_category'] ?? '',
            'options'         => $formattedOptions,
            'marks'           => $marks,
        ];

        $this->showQuestionPreview = true;
    }

    public function closeQuestionPreview(): void
    {
        $this->showQuestionPreview = false;
        $this->previewQuestion     = [];
    }
    /* ================================================================
     | RENDER
     * ================================================================*/
    public function render()
    {
        return view('livewire.admin.questions.question-group-form', [
            'languages'         => Globals::LANGUAGES,
            'primarySkillTypes' => $this->primarySkillTypes,
            'subSkillTypes'     => $this->subSkillTypes,
            'difficultyLevels'  => $this->difficultyLevels,
            'ageGroups'         => $this->ageGroups,
        ]);
    }
}
