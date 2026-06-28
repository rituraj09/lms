<?php

namespace App\Livewire\Admin\AssessmentMasters;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AssessmentMaster\Assessment;
use App\Models\AssessmentMaster\AssessmentGroup;
use App\Models\AssessmentMaster\AssessmentQuestion;
use App\Models\QuestionMaster\QuestionGroup;
use App\Models\QuestionMaster\Question;
use App\Helper\Globals;

#[Layout('layouts.backend')]
class AssessmentBuild extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $view = 'builder';

    #[Locked]
    public ?int $assessmentId = null;

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
    public int    $max_attempts       = 1;
    public bool   $shuffle_sections   = false;
    public bool   $show_result_immediately = true;
    public bool   $show_correct_answers    = false;
    public bool   $show_explainations      = false;

    public array $assessmentGroups = [];

    // Group Picker
    public string $pickerMode         = 'new';
    public ?int   $pickerAgIndex      = null;
    public string $groupPickerSearch  = '';
    public ?int   $pickerGroupId      = null;
    public array  $pickerSelectedQIds = [];

    // Question Preview Modal
    public bool  $showQuestionPreview = false;
    public array $previewQuestion     = [];

    public array $languages = [];

    /* ================================================================
     |  MOUNT
     * ================================================================*/
    public function mount(string $id): void
    {
        $this->languages = array_keys(Globals::LANGUAGES);

        $decryptedId = decrypt($id);
        $assessment  = Assessment::findOrFail($decryptedId);

        if ($assessment->isLockedForEditing()) {
            session()->flash(
                'error',
                'This assessment is locked for editing because students have already attempted it.'
            );
            $this->redirect(route('admin.assessment-masters.list'), navigate: false);
            return;
        }

        $this->assessmentId            = $decryptedId;
        $this->assessment_code         = $assessment->assessment_code;
        $this->title                   = $assessment->title;
        $this->instructions            = $assessment->instructions ?? '';
        $this->assessment_type_id      = $assessment->assessment_type_id;
        $this->age_group_id            = $assessment->age_group_id;
        $this->total_marks             = (float) $assessment->total_marks;
        $this->passing_marks           = (float) $assessment->passing_marks;
        $this->duration_minutes        = $assessment->duration_minutes;
        $this->admin_note              = $assessment->admin_note ?? '';
        $this->has_negative_mark       = (bool) $assessment->has_negative_mark;
        $this->status                  = $assessment->status;
        $this->max_attempts            = (int)  $assessment->max_attempts;
        $this->shuffle_sections        = (bool) $assessment->shuffle_sections;
        $this->show_result_immediately = (bool) $assessment->show_result_immediately;
        $this->show_correct_answers    = (bool) $assessment->show_correct_answers;
        $this->show_explainations      = (bool) $assessment->show_explainations;

        $this->loadBuilder($decryptedId);
    }

    /* ================================================================
     |  LOAD BUILDER
     * ================================================================*/
    private function loadBuilder(int $assessmentId): void
    {
        $groups = AssessmentGroup::where('assessment_id', $assessmentId)
            ->with([
                'questionGroup',
                'assessmentQuestions.question',
            ])
            ->get();

        $this->assessmentGroups = [];

        foreach ($groups as $ag) {
            $qg          = $ag->questionGroup;
            $qgContent   = $qg->group_content ?? [];
            $defaultLang = $this->languages[0] ?? 'en';

            $questions = [];
            foreach ($ag->assessmentQuestions as $aq) {
                $q        = $aq->question;
                $qContent = $q->question_content ?? [];
                $stemEn   = $qContent['stem'][$defaultLang] ?? '';

                $questions[] = [
                    'assessment_question_id' => $aq->id,
                    'question_id'            => $q->id,
                    'question_code'          => $q->question_code,
                    'stem_en'                => $stemEn,
                    'marks'                  => $qContent['marks'] ?? $q->marks ?? 0,
                    'answer_category'        => $qContent['answer_category'] ?? $q->answer_category ?? '',
                    'negative_mark'          => $aq->negative_mark ?? 0,
                    'question_timer'         => $aq->question_timer ?? 0,
                ];
            }

            $this->assessmentGroups[] = [
                'assessment_group_id'             => $ag->id,
                'question_group_id'               => $qg->id,
                'group_code'                      => $qg->group_code,
                'group_title'                     => $qgContent['title'][$defaultLang] ?? $qg->group_code,
                'group_content'                   => $qgContent,
                'questions_category'              => $qg->questions_category,
                'instructions'                    => $ag->instructions ?? '',
                'suffle_question'                 => (bool) $ag->suffle_question,
                'allow_back_to_group_question'    => (bool) $ag->allow_back_to_group_question,
                'allow_back_to_previous_question' => (bool) $ag->allow_back_to_previous_question,
                'group_timer'                     => $ag->group_timer ?? 0,
                'admin_note'                      => $ag->admin_note ?? '',
                'questions'                       => $questions,
            ];
        }
    }

    /* ================================================================
     |  SAVE BUILDER
     * ================================================================*/
    public function saveBuilder(): void
    {
        if ($this->assessmentId) {
            $existing = Assessment::find($this->assessmentId);
            if ($existing && $existing->isLockedForEditing()) {
                $this->addError(
                    'builder',
                    'Cannot modify questions because students have already attempted this assessment.'
                );
                return;
            }
        }

        if (! $this->assessmentId) {
            $this->addError('builder', 'Please save assessment info first.');
            return;
        }

        if (empty($this->assessmentGroups)) {
            $this->addError('builder', 'Please add at least one question group.');
            return;
        }

        try {
            DB::transaction(function () {
                $totalMarks = 0;

                foreach ($this->assessmentGroups as $agIndex => $agData) {
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

                    foreach ($agData['questions'] as $qIndex => $qData) {
                        $aqId = $qData['assessment_question_id'] ?? null;

                        $aqPayload = [
                            'assessment_group_id' => $ag->id,
                            'question_id'         => $qData['question_id'],
                            'negative_mark'       => (float) ($qData['negative_mark'] ?? 0),
                            'question_timer'      => (int) ($qData['question_timer'] ?? 0),
                            'updated_by'          => auth()->id(),
                        ];

                        if ($aqId) {
                            AssessmentQuestion::where('id', $aqId)->update($aqPayload);
                        } else {
                            $aqPayload['created_by'] = auth()->id();
                            AssessmentQuestion::create($aqPayload);
                        }

                        $totalMarks += (float) ($qData['marks'] ?? 0);
                    }
                }

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
     |  GROUP PICKER — FULL PAGE
     * ================================================================*/
    public function openGroupPicker(): void
    {
        $this->pickerMode         = 'new';
        $this->pickerAgIndex      = null;
        $this->pickerGroupId      = null;
        $this->pickerSelectedQIds = [];
        $this->groupPickerSearch  = '';
        $this->view               = 'group-picker';
        $this->resetPage();
    }

    public function openAddMoreQuestions(int $agIndex): void
    {
        $this->pickerMode         = 'existing';
        $this->pickerAgIndex      = $agIndex;
        $this->pickerGroupId      = $this->assessmentGroups[$agIndex]['question_group_id'];
        $this->pickerSelectedQIds = [];
        $this->groupPickerSearch  = '';
        $this->view               = 'group-picker';
        $this->resetPage();
    }

    public function closeGroupPicker(): void
    {
        $this->view               = 'builder';
        $this->pickerGroupId      = null;
        $this->pickerSelectedQIds = [];
        $this->pickerMode         = 'new';
        $this->pickerAgIndex      = null;
        $this->resetPage();
    }

    public function selectPickerGroup(int $groupId): void
    {
        $this->pickerGroupId      = $groupId;
        $this->pickerSelectedQIds = [];
        $this->resetPage();
    }

    public function togglePickerQuestion(int $questionId): void
    {
        if (in_array($questionId, $this->pickerSelectedQIds)) {
            $this->pickerSelectedQIds = array_values(
                array_filter($this->pickerSelectedQIds, fn($id) => $id !== $questionId)
            );
        } else {
            $this->pickerSelectedQIds[] = $questionId;
        }
    }

    public function addGroupToAssessment(): void
    {
        if (! $this->pickerGroupId || empty($this->pickerSelectedQIds)) {
            return;
        }

        $defaultLang = $this->languages[0] ?? 'en';

        if ($this->pickerMode === 'new') {
            $group     = QuestionGroup::with('questions')->findOrFail($this->pickerGroupId);
            $qgContent = $group->group_content ?? [];

            $questions = [];
            foreach ($this->pickerSelectedQIds as $qId) {
                $q = $group->questions->firstWhere('id', $qId);
                if (! $q) continue;
                $content = $q->question_content ?? [];

                $questions[] = [
                    'assessment_question_id' => null,
                    'question_id'            => $q->id,
                    'question_code'          => $q->question_code,
                    'stem_en'                => $content['stem'][$defaultLang] ?? '',
                    'marks'                  => $content['marks'] ?? $q->marks ?? 0,
                    'answer_category'        => $content['answer_category'] ?? $q->answer_category ?? '',
                    'negative_mark'          => 0,
                    'question_timer'         => 0,
                ];
            }

            $this->assessmentGroups[] = [
                'assessment_group_id'             => null,
                'question_group_id'               => $group->id,
                'group_code'                      => $group->group_code,
                'group_title'                     => $qgContent['title'][$defaultLang] ?? $group->group_code,
                'group_content'                   => $qgContent,
                'questions_category'              => $group->questions_category,
                'instructions'                    => '',
                'suffle_question'                 => false,
                'allow_back_to_group_question'    => true,
                'allow_back_to_previous_question' => true,
                'group_timer'                     => 0,
                'admin_note'                      => '',
                'questions'                       => $questions,
            ];

        } else {
            $group = QuestionGroup::with('questions')->findOrFail($this->pickerGroupId);

            foreach ($this->pickerSelectedQIds as $qId) {
                $q = $group->questions->firstWhere('id', $qId);
                if (! $q) continue;
                $content = $q->question_content ?? [];

                $this->assessmentGroups[$this->pickerAgIndex]['questions'][] = [
                    'assessment_question_id' => null,
                    'question_id'            => $q->id,
                    'question_code'          => $q->question_code,
                    'stem_en'                => $content['stem'][$defaultLang] ?? '',
                    'marks'                  => $content['marks'] ?? $q->marks ?? 0,
                    'answer_category'        => $content['answer_category'] ?? $q->answer_category ?? '',
                    'negative_mark'          => 0,
                    'question_timer'         => 0,
                ];
            }
        }

        $this->closeGroupPicker();
    }

    public function removeAssessmentGroup(int $agIndex): void
    {
        $ag = $this->assessmentGroups[$agIndex] ?? null;

        if ($ag && $ag['assessment_group_id']) {
            AssessmentGroup::where('id', $ag['assessment_group_id'])->delete();
        }

        array_splice($this->assessmentGroups, $agIndex, 1);
    }

    public function removeQuestionFromGroup(int $agIndex, int $qIndex): void
    {
        $q = $this->assessmentGroups[$agIndex]['questions'][$qIndex] ?? null;

        if ($q && ($q['assessment_question_id'] ?? null)) {
            AssessmentQuestion::where('id', $q['assessment_question_id'])->delete();
        }

        array_splice($this->assessmentGroups[$agIndex]['questions'], $qIndex, 1);
    }

    /* ================================================================
     |  QUESTION PREVIEW MODAL
     * ================================================================*/
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
     |  COMPUTED — Picker Groups
     * ================================================================*/
    public function getPickerGroupsProperty()
    {
        $usedMultipleGroupIds = collect($this->assessmentGroups)
            ->where('questions_category', 'multiple')
            ->pluck('question_group_id')
            ->toArray();

        return QuestionGroup::withCount('questions')
            ->when($this->groupPickerSearch, fn($q) =>
            $q->where('group_code', 'like', "%{$this->groupPickerSearch}%")
            )
            ->get()
            ->map(function ($pg) use ($usedMultipleGroupIds) {
                $pg->is_used_multiple = in_array($pg->id, $usedMultipleGroupIds);
                return $pg;
            });
    }

    /* ================================================================
     |  COMPUTED — Picker Questions (Paginated)
     * ================================================================*/
    public function getPickerQuestionsProperty()
    {
        if (! $this->pickerGroupId) {
            return collect();
        }

        $usedQuestionIds = collect($this->assessmentGroups)
            ->flatMap(fn($ag) => collect($ag['questions'])->pluck('question_id'))
            ->toArray();

        if ($this->pickerMode === 'existing' && $this->pickerAgIndex !== null) {
            $currentGroupQIds = collect(
                $this->assessmentGroups[$this->pickerAgIndex]['questions']
            )->pluck('question_id')->toArray();

            $usedQuestionIds = array_diff($usedQuestionIds, $currentGroupQIds);
        }

        return Question::where('question_group_id', $this->pickerGroupId)
            ->whereNotIn('id', $usedQuestionIds)
            ->paginate(15);
    }

    /* ================================================================
     |  NAVIGATION
     * ================================================================*/
    public function backToForm(): void
    {
        $this->redirect(
            route('admin.assessment-masters.manage', ['id' => encrypt($this->assessmentId)]),
            navigate: false
        );
    }

    public function backToList(): void
    {
        $this->redirect(route('admin.assessment-masters.list'), navigate: false);
    }

    /* ================================================================
     |  RENDER
     * ================================================================*/
    public function render()
    {
        return view('livewire.admin.assessment-masters.assessment-build', [
            'languages'      => Globals::LANGUAGES,
            'pickerGroups'   => $this->view === 'group-picker' ? $this->pickerGroups : collect(),
            'pickerQuestions' => $this->view === 'group-picker' && $this->pickerGroupId
                ? $this->pickerQuestions
                : null,
        ]);
    }
}
