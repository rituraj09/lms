<?php

namespace App\Livewire\Admin\AssessmentMasters;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Models\AssessmentMaster\Assessment;
use App\Models\AssessmentMaster\AssessmentGroup;
use App\Models\QuestionMaster\Question;
use App\Helper\Globals;

#[Layout('layouts.backend')]
class AssessmentBuildView extends Component
{
    #[Locked]
    public ?int $assessmentId = null;

    public string $assessment_code         = '';
    public string $title                   = '';
    public string $instructions            = '';
    public string $assessment_type_id      = '';
    public ?int   $age_group_id            = null;
    public float  $total_marks             = 0;
    public float  $passing_marks           = 0;
    public ?int   $duration_minutes        = null;
    public string $admin_note              = '';
    public bool   $has_negative_mark       = false;
    public string $status                  = 'draft';
    public string $difficultLevel          = '';
    public string $ageGroup                = '';
    public int    $max_attempts            = 1;
    public bool   $shuffle_sections        = false;
    public bool   $show_result_immediately = true;
    public bool   $show_correct_answers    = false;
    public bool   $show_explainations      = false;

    public array $assessmentGroups = [];
    public array $languages        = [];

    // Question Preview Modal
    public bool  $showQuestionPreview = false;
    public array $previewQuestion     = [];

    /* ================================================================
     |  MOUNT
     * ================================================================*/
    public function mount(string $id): void
    {
        $this->languages = array_keys(Globals::LANGUAGES);

        $decryptedId = decrypt($id);
        $assessment  = Assessment::findOrFail($decryptedId);

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
        $this->difficultLevel          = $assessment->difficultyLevel->name;
        $this->ageGroup                = $assessment->ageGroup->name;

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
     |  QUESTION PREVIEW MODAL
     * ================================================================*/
    public function viewQuestion(int $questionId): void
    {
        $question = Question::with('questionGroup')->findOrFail($questionId);

        $adminNotes = is_array($question->admin_notes)
            ? $question->admin_notes
            : json_decode($question->admin_notes ?? '{}', true);

        $adminNote = $adminNotes['note'] ?? '';

        $raw     = $question->getRawOriginal('question_content');
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
            'admin_note'      => $adminNote,
        ];

        $this->showQuestionPreview = true;
    }

    public function closeQuestionPreview(): void
    {
        $this->showQuestionPreview = false;
        $this->previewQuestion     = [];
    }

    /* ================================================================
     |  RENDER
     * ================================================================*/
    public function render()
    {
        return view('livewire.admin.assessment-masters.assessment-build-view', [
            'languages' => Globals::LANGUAGES,
        ]);
    }
}
