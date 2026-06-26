<?php

namespace App\Livewire\Admin\Assessments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\AssessmentMaster\Assessment;
use App\Models\AssessmentMaster\AssessmentGroup;
use App\Models\AssessmentMaster\AssessmentQuestion;
use App\Models\QuestionMaster\QuestionGroup;
use App\Models\QuestionMaster\Question;
use App\Models\EvaluationMaster\AgeGroup;
use App\Models\EvaluationMaster\QuestionType;
use App\Helper\Globals;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.backend')]
class AssessmentManager extends Component
{
    use WithFileUploads;
    /* ================================================================
     |  VIEW STATES
     |  'list'     → Assessment listing
     |  'form'     → Create / Edit assessment basic info
     |  'builder'  → Add question groups + questions to assessment
     * ================================================================*/
    public string $view = 'list';

    /* ================================================================
     |  LOCKED
     * ================================================================*/
    #[Locked]
    public ?int $assessmentId = null;

    /* ================================================================
     |  LIST VIEW — Filters
     * ================================================================*/
    public string $search       = '';
    public string $statusFilter = '';
    public string $typeFilter   = '';
    public int    $perPage      = 10;

    /* ================================================================
     |  ASSESSMENT FORM FIELDS
     * ================================================================*/
    public string $assessment_code    = '';
    public string $title              = '';
    public string $instructions       = '';
    public string $assessment_type_id = '';
    public ?int   $age_group_id       = null;
    public float  $total_marks        = 0;
    public float  $passing_marks      = 0;
    public ?int   $duration_minutes   = null;
    public string $admin_note         = '';
    public bool   $has_negative_mark  = false;
    public string $status             = 'draft';

    /* ================================================================
     |  BUILDER STATE
     * ================================================================*/
    public array $assessmentGroups = [];

    /* ================================================================
     |  QUESTION GROUP PICKER STATE
     |
     |  $pickerMode:
     |    'new'      → adding a brand new group to assessment
     |    'existing' → adding more questions to an existing ag index
     * ================================================================*/
    public bool   $showGroupPicker    = false;
    public string $pickerMode         = 'new';
    public ?int   $pickerAgIndex      = null;   // agIndex for 'existing' mode
    public string $groupPickerSearch  = '';
    public ?int   $pickerGroupId      = null;
    public array  $pickerQuestions    = [];
    public array  $pickerSelectedQIds = [];

    /* ================================================================
     |  DROPDOWN DATA
     * ================================================================*/
    public array $ageGroups      = [];
    public array $questionTypes  = [];
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
     |  LANGUAGES
     * ================================================================*/
    protected array $languages = [];

    // Image upload
    public $cover_image_file = null;        // Livewire temp upload
    public string $cover_image_path  = '';  // Stored path from DB
    public bool $removeCoverImage    = false;
    /* ================================================================
     |  NEW ASSESSMENT SETTINGS (from migration)
     * ================================================================*/
    public int  $max_attempts            = 1;
    public bool $shuffle_sections        = false;
    public bool $show_result_immediately = true;
    public bool $show_correct_answers    = false;
    public bool $show_explainations      = false;
    /* ================================================================
     |  MOUNT
     * ================================================================*/
    public function mount(): void
    {
        $this->languages = array_keys(Globals::LANGUAGES);

        $this->ageGroups = AgeGroup::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->questionTypes = QuestionType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->toArray();
    }

    /* ================================================================
     |  COMPUTED — Assessment List
     * ================================================================*/
    public function getAssessmentsProperty()
    {
        return Assessment::with(['ageGroup', 'createdBy'])
            ->withCount('assessmentGroups')
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
        $this->resetForm();
        $this->assessment_code = $this->generateAssessmentCode();
        $this->view            = 'form';
    }

    public function editAssessment(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        $this->assessmentId          = $id;
        $this->assessment_code       = $assessment->assessment_code;
        $this->title                 = $assessment->title;
        $this->instructions          = $assessment->instructions ?? '';
        $this->assessment_type_id    = $assessment->assessment_type_id;
        $this->age_group_id          = $assessment->age_group_id;
        $this->total_marks           = (float) $assessment->total_marks;
        $this->passing_marks         = (float) $assessment->passing_marks;
        $this->duration_minutes      = $assessment->duration_minutes;
        $this->admin_note            = $assessment->admin_note ?? '';
        $this->has_negative_mark     = (bool) $assessment->has_negative_mark;
        $this->status                = $assessment->status;
        $this->cover_image_path      = $assessment->cover_image ?? '';
        $this->cover_image_file      = null;
        $this->removeCoverImage      = false;
        // ── New fields ──
        $this->max_attempts            = (int)  $assessment->max_attempts;
        $this->shuffle_sections        = (bool) $assessment->shuffle_sections;
        $this->show_result_immediately = (bool) $assessment->show_result_immediately;
        $this->show_correct_answers    = (bool) $assessment->show_correct_answers;
        $this->show_explainations      = (bool) $assessment->show_explainations;
        // ────────────────
        $this->view = 'form';
    }

    public function openBuilder(int $id): void
    {
        // Load assessment info first
        $assessment = Assessment::findOrFail($id);

        $this->assessmentId          = $id;
        $this->assessment_code       = $assessment->assessment_code;
        $this->title                 = $assessment->title;
        $this->instructions          = $assessment->instructions ?? '';
        $this->assessment_type_id    = $assessment->assessment_type_id;
        $this->age_group_id          = $assessment->age_group_id;
        $this->total_marks           = (float) $assessment->total_marks;
        $this->passing_marks         = (float) $assessment->passing_marks;
        $this->duration_minutes      = $assessment->duration_minutes;
        $this->admin_note            = $assessment->admin_note ?? '';
        $this->has_negative_mark     = (bool) $assessment->has_negative_mark;
        $this->status                = $assessment->status;
        // ── New fields ──
        $this->max_attempts            = (int)  $assessment->max_attempts;
        $this->shuffle_sections        = (bool) $assessment->shuffle_sections;
        $this->show_result_immediately = (bool) $assessment->show_result_immediately;
        $this->show_correct_answers    = (bool) $assessment->show_correct_answers;
        $this->show_explainations      = (bool) $assessment->show_explainations;
        // ────────────────
        $this->loadBuilder($id);
        $this->view = 'builder';

    }

    public function deleteAssessment(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        if ($assessment->status === 'publish') {
            session()->flash('error', 'Cannot delete a published assessment.');
            return;
        }

        $assessment->delete();
        session()->flash('success', 'Assessment deleted successfully.');
    }

    public function toggleStatus(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        $assessment->status = $assessment->status === 'publish'
            ? 'unpublish'
            : 'publish';

        $assessment->save();
        session()->flash('success', 'Assessment status updated.');
    }

    /* ================================================================
     |  ASSESSMENT FORM — Save
     * ================================================================*/
    public function saveAssessment(): void
    {
        $this->validate(
            [
                'title'                  => 'required|string|max:500',
                'cover_image_file'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'assessment_type_id'     => 'required|string',
                'age_group_id'           => 'required|integer',
                'passing_marks'          => 'required|numeric|min:0',
                'duration_minutes'       => 'required|numeric|min:0',
                'status'                 => 'required|in:draft,publish,unpublish',
                // ── New fields ──
                'max_attempts'           => 'required|integer|min:1',
                'shuffle_sections'       => 'boolean',
                'show_result_immediately'=> 'boolean',
                'show_correct_answers'   => 'boolean',
                'show_explainations'     => 'boolean',
            ],
            [
                'title.required'              => 'Assessment title is required.',
                'cover_image_file.image'      => 'Cover image must be an image file.',
                'cover_image_file.mimes'      => 'Accepted formats: jpg, jpeg, png, webp.',
                'cover_image_file.max'        => 'Cover image must not exceed 2MB.',
                'assessment_type_id.required' => 'Please select an assessment type.',
                'age_group_id.required'       => 'Please select an age group.',
                'passing_marks.required'      => 'Passing marks are required.',
                'max_attempts.required'       => 'Max attempts is required.',
                'max_attempts.min'            => 'Max attempts must be at least 1.',
            ]
        );
        // ── Handle Cover Image ──────────────────────────────────────
        $coverImagePath = $this->cover_image_path ?: null;

        if ($this->removeCoverImage) {
            // Delete old file if exists
            if ($this->cover_image_path && Storage::disk('public')->exists($this->cover_image_path)) {
                Storage::disk('public')->delete($this->cover_image_path);
            }
            $coverImagePath = null;
        }

        if ($this->cover_image_file) {
            // Delete old file before storing new one
            if ($this->cover_image_path && Storage::disk('public')->exists($this->cover_image_path)) {
                Storage::disk('public')->delete($this->cover_image_path);
            }
            $coverImagePath = $this->cover_image_file->store('assessments/covers', 'public');
        }
        try {
            $payload = [
                'assessment_code'        => $this->assessment_code,
                'title'                  => $this->title,
                'cover_image'            => $coverImagePath,
                'instructions'           => $this->instructions,
                'assessment_type_id'     => $this->assessment_type_id,
                'age_group_id'           => $this->age_group_id,
                'total_marks'            => $this->total_marks,
                'passing_marks'          => $this->passing_marks,
                'duration_minutes'       => !empty($this->duration_minutes)
                    ? (int) $this->duration_minutes
                    : null,
                'admin_note'             => $this->admin_note,
                'has_negative_mark'      => (bool) $this->has_negative_mark,
                'status'                 => $this->status,
                // ── New fields ──
                'max_attempts'           => (int)  $this->max_attempts,
                'shuffle_sections'       => (bool) $this->shuffle_sections,
                'show_result_immediately'=> (bool) $this->show_result_immediately,
                'show_correct_answers'   => (bool) $this->show_correct_answers,
                'show_explainations'     => (bool) $this->show_explainations,
                // ────────────────
                'updated_by'             => auth()->id(),
            ];

            if ($this->assessmentId) {
                Assessment::where('id', $this->assessmentId)->update($payload);
                $assessment = Assessment::find($this->assessmentId);
            } else {
                $payload['created_by'] = auth()->id();
                $assessment            = Assessment::create($payload);
                $this->assessmentId    = $assessment->id;
            }

            $this->loadBuilder($assessment->id);
            $this->view = 'builder';

            session()->flash('success', 'Assessment saved. Now add question groups.');

        } catch (\Throwable $e) {
            Log::error('saveAssessment failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            $this->addError('save_failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function removeCoverImage(): void
    {
        $this->removeCoverImage  = true;
        $this->cover_image_file  = null;
        $this->cover_image_path  = '';
    }
    /* ================================================================
     |  BUILDER — Load existing groups + questions
     * ================================================================*/
    private function loadBuilder(int $assessmentId): void
    {
        $this->assessmentGroups = [];

        $groups = AssessmentGroup::with([
                'questionGroup',
                'assessmentQuestions.question',
            ])
            ->where('assessment_id', $assessmentId)
            ->orderBy('id')
            ->get();

        foreach ($groups as $ag) {
            $qg      = $ag->questionGroup;
            $content = $qg->group_content ?? [];

            $questions = [];

            foreach ($ag->assessmentQuestions as $aq) {
                $q        = $aq->question;
                $qContent = $q->question_content ?? [];

                $questions[] = [
                    'assessment_question_id' => $aq->id,
                    'question_id'            => $q->id,
                    'question_code'          => $q->question_code,
                    'stem_en'                => strip_tags($qContent['stem']['en'] ?? ''),
                    'answer_category'        => $q->answer_category,
                    'marks'                  => (float) ($qContent['marks'] ?? 0),
                    'negative_mark'          => (float) ($aq->negative_mark ?? 0),
                    'question_timer'         => (int) ($aq->question_timer ?? 0),
                    'question_type_id'       => $aq->question_type_id,
                ];
            }

            $this->assessmentGroups[] = [
                'assessment_group_id'             => $ag->id,
                'question_group_id'               => $qg->id,
                'group_code'                      => $qg->group_code,
                'group_title'                     => $content['title'][array_key_first(Globals::LANGUAGES)] ?? $qg->group_code,
                'questions_category'              => $qg->questions_category,
                'group_content'                   => $content,
                'instructions'                    => $ag->instructions ?? '',
                'suffle_question'                 => (bool) $ag->suffle_question,
                'allow_back_to_group_question'    => (bool) $ag->allow_back_to_group_question,
                'allow_back_to_previous_question' => (bool) $ag->allow_back_to_previous_question,
                'group_timer'                     => (int) ($ag->group_timer ?? 0),
                'admin_note'                      => $ag->admin_note ?? '',
                'questions'                       => $questions,
            ];
        }
    }

    /* ================================================================
     |  BUILDER — Open picker for NEW group
     * ================================================================*/
    public function openGroupPicker(): void
    {
        $this->pickerMode         = 'new';
        $this->pickerAgIndex      = null;
        $this->pickerGroupId      = null;
        $this->showGroupPicker    = true;
        $this->groupPickerSearch  = '';
        $this->pickerQuestions    = [];
        $this->pickerSelectedQIds = [];
        $this->resetErrorBag();
    }

    /* ================================================================
     |  BUILDER — Open picker to add MORE questions to existing group
     * ================================================================*/
    public function openAddMoreQuestions(int $agIndex): void
    {
        $ag = $this->assessmentGroups[$agIndex];

        $this->pickerMode    = 'existing';
        $this->pickerAgIndex = $agIndex;

        // Pre-select the group — lock it to this group only
        $this->pickerGroupId     = $ag['question_group_id'];
        $this->showGroupPicker   = true;
        $this->groupPickerSearch = '';
        $this->pickerSelectedQIds = [];
        $this->resetErrorBag();

        // Load questions for this group
        $this->loadPickerQuestionsForGroup($ag['question_group_id'], $agIndex);
    }

    /* ================================================================
     |  BUILDER — Close picker
     * ================================================================*/
    public function closeGroupPicker(): void
    {
        $this->showGroupPicker    = false;
        $this->pickerMode         = 'new';
        $this->pickerAgIndex      = null;
        $this->pickerGroupId      = null;
        $this->pickerQuestions    = [];
        $this->pickerSelectedQIds = [];
    }

    /* ================================================================
     |  COMPUTED — Picker Groups (only in 'new' mode)
     * ================================================================*/
    public function getPickerGroupsProperty(): \Illuminate\Support\Collection
    {
        return QuestionGroup::with('questions')
            ->when($this->groupPickerSearch, fn($q) =>
                $q->where('title', 'like', "%{$this->groupPickerSearch}%")
                  ->orWhere('group_code', 'like', "%{$this->groupPickerSearch}%")
            )
            ->withCount('questions')
            ->orderBy('group_code')
            ->get();
    }

    /* ================================================================
     |  BUILDER — Select group in picker (only for 'new' mode)
     * ================================================================*/
    public function selectPickerGroup(int $groupId): void
    {
        // Only allowed in 'new' mode
        if ($this->pickerMode === 'existing') {
            return;
        }

        $group = QuestionGroup::with('questions')->findOrFail($groupId);

        // 'multiple' category — only once per assessment
        if ($group->questions_category === 'multiple') {
            $alreadyAdded = collect($this->assessmentGroups)
                ->where('question_group_id', $groupId)
                ->count();

            if ($alreadyAdded > 0) {
                $this->addError(
                    'picker',
                    'This passage group can only be added once per assessment.'
                );
                return;
            }
        }

        $this->pickerGroupId      = $groupId;
        $this->pickerSelectedQIds = [];

        $this->loadPickerQuestionsForGroup($groupId, null);
    }

    /* ================================================================
     |  INTERNAL — Load picker questions for a group
     |  $agIndex = null means 'new' mode (no existing ag to compare)
     * ================================================================*/
    private function loadPickerQuestionsForGroup(int $groupId, ?int $agIndex): void
    {
        $group = QuestionGroup::with('questions')->findOrFail($groupId);

        // Collect already-used question IDs from the target ag (existing mode)
        // OR from ALL ags with same group_id (new mode)
        if ($agIndex !== null) {
            // existing mode — only check THIS specific ag
            $alreadyUsedQIds = collect(
                $this->assessmentGroups[$agIndex]['questions'] ?? []
            )
            ->pluck('question_id')
            ->values()
            ->toArray();
        } else {
            // new mode — check all ags with same group_id
            $alreadyUsedQIds = collect($this->assessmentGroups)
                ->where('question_group_id', $groupId)
                ->flatMap(fn($ag) => collect($ag['questions'])->pluck('question_id'))
                ->values()
                ->toArray();
        }

        $this->pickerQuestions = $group->questions->map(function ($q) use ($alreadyUsedQIds) {
            $qContent = $q->question_content ?? [];

            return [
                'id'              => $q->id,
                'question_code'   => $q->question_code,
                'stem_en'         => strip_tags($qContent['stem']['en'] ?? ''),
                'answer_category' => $q->answer_category,
                'marks'           => (float) ($qContent['marks'] ?? 0),
                'already_used'    => in_array($q->id, $alreadyUsedQIds),
            ];
        })->toArray();
    }

    /* ================================================================
     |  BUILDER — Toggle question selection in picker
     * ================================================================*/
    public function togglePickerQuestion(int $questionId): void
    {
        if (in_array($questionId, $this->pickerSelectedQIds)) {
            $this->pickerSelectedQIds = array_values(
                array_filter(
                    $this->pickerSelectedQIds,
                    fn($id) => $id !== $questionId
                )
            );
        } else {
            $this->pickerSelectedQIds[] = $questionId;
        }
    }

    /* ================================================================
     |  BUILDER — Add selected questions to assessment
     * ================================================================*/
    public function addGroupToAssessment(): void
    {
        if (! $this->pickerGroupId || empty($this->pickerSelectedQIds)) {
            $this->addError('picker', 'Please select at least one question.');
            return;
        }

        $group   = QuestionGroup::with('questions')->findOrFail($this->pickerGroupId);
        $content = $group->group_content ?? [];

        // Build new questions array from selection
        $newQuestions = [];

        foreach ($this->pickerSelectedQIds as $qId) {
            $question = $group->questions->firstWhere('id', $qId);

            if (! $question) continue;

            $qContent = $question->question_content ?? [];

            $newQuestions[] = [
                'assessment_question_id' => null,
                'question_id'            => $question->id,
                'question_code'          => $question->question_code,
                'stem_en'                => strip_tags($qContent['stem']['en'] ?? ''),
                'answer_category'        => $question->answer_category,
                'marks'                  => (float) ($qContent['marks'] ?? 0),
                'negative_mark'          => 0,
                'question_timer'         => 0,
                'question_type_id'       => null,
            ];
        }

        if ($this->pickerMode === 'existing' && $this->pickerAgIndex !== null) {

            /* ── ADD MORE QUESTIONS to existing ag ──────────────── */
            foreach ($newQuestions as $nq) {
                $this->assessmentGroups[$this->pickerAgIndex]['questions'][] = $nq;
            }

        } else {

            /* ── ADD AS NEW GROUP ───────────────────────────────── */
            $this->assessmentGroups[] = [
                'assessment_group_id'             => null,
                'question_group_id'               => $group->id,
                'group_code'                      => $group->group_code,
                'group_title'                     => $content['title'][array_key_first(Globals::LANGUAGES)] ?? $group->group_code,
                'questions_category'              => $group->questions_category,
                'group_content'                   => $content,
                'instructions'                    => '',
                'suffle_question'                 => false,
                'allow_back_to_group_question'    => true,
                'allow_back_to_previous_question' => true,
                'group_timer'                     => 0,
                'admin_note'                      => '',
                'questions'                       => $newQuestions,
            ];
        }

        $this->closeGroupPicker();
        $this->recalculateTotalMarks();
    }

    /* ================================================================
     |  BUILDER — Remove Group from assessment
     * ================================================================*/
    public function removeAssessmentGroup(int $agIndex): void
    {
        $ag = $this->assessmentGroups[$agIndex];

        if (! empty($ag['assessment_group_id'])) {
            AssessmentGroup::find($ag['assessment_group_id'])?->delete();
        }

        unset($this->assessmentGroups[$agIndex]);
        $this->assessmentGroups = array_values($this->assessmentGroups);
        $this->recalculateTotalMarks();
    }

    /* ================================================================
     |  BUILDER — Remove Question from group
     * ================================================================*/
    public function removeQuestionFromGroup(int $agIndex, int $qIndex): void
    {
        $aq = $this->assessmentGroups[$agIndex]['questions'][$qIndex];

        if (! empty($aq['assessment_question_id'])) {
            AssessmentQuestion::find($aq['assessment_question_id'])?->delete();
        }

        unset($this->assessmentGroups[$agIndex]['questions'][$qIndex]);
        $this->assessmentGroups[$agIndex]['questions']
            = array_values($this->assessmentGroups[$agIndex]['questions']);

        $this->recalculateTotalMarks();
    }

    /* ================================================================
     |  BUILDER — Recalculate total marks
     * ================================================================*/
    private function recalculateTotalMarks(): void
    {
        $total = 0;

        foreach ($this->assessmentGroups as $ag) {
            foreach ($ag['questions'] as $q) {
                $total += (float) $q['marks'];
            }
        }

        $this->total_marks = $total;
    }

    /* ================================================================
     |  BUILDER — Save All Groups + Questions
     * ================================================================*/
    public function saveBuilder(): void
    {
        if (! $this->assessmentId) {
            $this->addError('builder', 'Please save assessment info first.');
            return;
        }

        if (empty($this->assessmentGroups)) {
            $this->addError('builder', 'Please add at least one question group.');
            return;
        }

        /* ── Validate question_type_id required for all questions ── */
        $validationRules    = [];
        $validationMessages = [];

        foreach ($this->assessmentGroups as $agIndex => $ag) {
            foreach ($ag['questions'] as $qIndex => $q) {
                $key = "assessmentGroups.{$agIndex}.questions.{$qIndex}.question_type_id";

                $validationRules[$key] = 'required|integer';

                $validationMessages["{$key}.required"] =
                    'Group ' . ($agIndex + 1) . ', Question ' . ($qIndex + 1) .
                    " ({$q['question_code']}): Question Type is required.";
            }
        }

        if (! empty($validationRules)) {
            $this->validate($validationRules, $validationMessages);
        }

        try {
            DB::transaction(function () {

                $totalMarks = 0;

                foreach ($this->assessmentGroups as $agIndex => $agData) {

                    /* ── Assessment Group upsert ──────────────── */
                    $agId = $agData['assessment_group_id'] ?? null;

                    $agPayload = [
                        'assessment_id'                   => $this->assessmentId,
                        'question_group_id'               => $agData['question_group_id'],
                        'instructions'                    => $agData['instructions'] ?? '',
                        'suffle_question'                 => (bool) ($agData['suffle_question'] ?? false),
                        'allow_back_to_group_question'    => (bool) ($agData['allow_back_to_group_question'] ?? true),
                        'allow_back_to_previous_question' => (bool) ($agData['allow_back_to_previous_question'] ?? true),
                        'group_timer'                     => (int) ($agData['group_timer'] ?? 0),
                        'admin_note'                      => $agData['admin_note'] ?? '',
                        'updated_by'                      => auth()->id(),
                    ];

                    if ($agId) {
                        AssessmentGroup::where('id', $agId)->update($agPayload);
                        $ag = AssessmentGroup::find($agId);
                    } else {
                        $agPayload['created_by'] = auth()->id();
                        $ag = AssessmentGroup::create($agPayload);
                        $this->assessmentGroups[$agIndex]['assessment_group_id'] = $ag->id;
                    }

                    /* ── Assessment Questions upsert ──────────── */
                    foreach ($agData['questions'] as $qIndex => $qData) {

                        $aqId = $qData['assessment_question_id'] ?? null;

                        $aqPayload = [
                            'assessment_group_id' => $ag->id,
                            'question_id'         => $qData['question_id'],
                            'negative_mark'       => (float) ($qData['negative_mark'] ?? 0),
                            'question_timer'      => (int) ($qData['question_timer'] ?? 0),
                            'question_type_id'    => (int) $qData['question_type_id'],
                            'updated_by'          => auth()->id(),
                        ];

                        if ($aqId) {
                            AssessmentQuestion::where('id', $aqId)->update($aqPayload);
                        } else {
                            $aqPayload['created_by'] = auth()->id();
                            $aq = AssessmentQuestion::create($aqPayload);
                            $this->assessmentGroups[$agIndex]['questions'][$qIndex]['assessment_question_id'] = $aq->id;
                        }

                        $totalMarks += (float) ($qData['marks'] ?? 0);
                    }
                }

                /* ── Update assessment total marks ────────────── */
                $this->total_marks = $totalMarks;

                Assessment::where('id', $this->assessmentId)->update([
                    'total_marks' => $totalMarks,
                    'updated_by'  => auth()->id(),
                ]);
            });

            session()->flash('success', 'Assessment builder saved successfully.');

        } catch (\Throwable $e) {
            Log::error('saveBuilder failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            $this->addError('save_failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /* ================================================================
     |  Negative Mark toggle — clear all when disabled
     * ================================================================*/
    public function updatedHasNegativeMark(): void
    {
        if (! $this->has_negative_mark) {
            foreach ($this->assessmentGroups as $agIndex => $ag) {
                foreach ($ag['questions'] as $qIndex => $_) {
                    $this->assessmentGroups[$agIndex]['questions'][$qIndex]['negative_mark'] = 0;
                }
            }
        }
    }

    /* ================================================================
     |  HELPERS
     * ================================================================*/
    private function generateAssessmentCode(): string
    {
        do {
            $code = 'ASS-' . strtoupper(Str::random(8));
        } while (Assessment::where('assessment_code', $code)->exists());

        return $code;
    }

    private function resetForm(): void
    {

        $this->assessmentId          = null;
        $this->assessment_code       = '';
        $this->title                 = '';
        $this->cover_image_file      = null;
        $this->cover_image_path      = '';
        $this->removeCoverImage      = false;
        $this->instructions          = '';
        $this->assessment_type_id    = '';
        $this->age_group_id          = null;
        $this->total_marks           = 0;
        $this->passing_marks         = 0;
        $this->duration_minutes      = null;
        $this->admin_note            = '';
        $this->has_negative_mark     = false;
        $this->status                = 'draft';
        // ── New fields ──
        $this->max_attempts            = 1;
        $this->shuffle_sections        = false;
        $this->show_result_immediately = true;
        $this->show_correct_answers    = false;
        $this->show_explainations      = false;
        // ────────────────
        $this->assessmentGroups      = [];
        $this->resetErrorBag();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->view = 'list';
    }

    public function backToForm(): void
    {
        $this->view = 'form';
    }

    public function backToList(): void
    {
        $this->resetForm();
        $this->view = 'list';
    }

    /* ================================================================
     |  RENDER
     * ================================================================*/
    public function render()
    {
        return view('livewire.admin.assessments.assessment-manager', [
            'assessments'   => $this->assessments,
            'languages'     => Globals::LANGUAGES,
            'questionTypes' => $this->questionTypes,
            'pickerGroups'  => ($this->showGroupPicker && $this->pickerMode === 'new')
                                ? $this->pickerGroups
                                : collect(),
        ]);
    }
}
