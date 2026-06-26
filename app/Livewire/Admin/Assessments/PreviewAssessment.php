<?php

namespace App\Livewire\Admin\Assessments;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\AssessmentMaster\Assessment;
use App\Models\AssessmentMaster\AssessmentGroup;
use App\Models\AssessmentMaster\AssessmentQuestion;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Crypt;

#[Layout('layouts.backend')]
class PreviewAssessment extends Component
{
    public Assessment $assessment;
    public $currentSectionIndex = 0;
    public $currentQuestionIndex = 0;
    public $language = 'en';
    public $availableLanguages = [];
    public $sections = [];
    public $currentSection = null;
    public $currentQuestion = null;
    public $answeredQuestions = [];

    protected $queryString = ['currentSectionIndex', 'currentQuestionIndex', 'language'];

    public function mount($encryptedId)
    {
        try {
            $assessmentId = Crypt::decrypt($encryptedId);
            $this->assessment = Assessment::findOrFail($assessmentId);
        } catch (\Exception $e) {
            abort(404);
        }

        // Check permission
        if (!Gate::allows('system.assessment.view')) {
            abort(403);
        }

        $this->loadSections();
        $this->setDefaultLanguage();
    }

    public function loadSections()
    {
        $this->sections = $this->assessment->assessmentGroups()
            ->with(['questionGroup', 'assessmentQuestions.question'])
            ->orderBy('id')
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->instructions ?? 'Section ' . ($this->sections->count() + 1),
                    'instructions' => $section->instructions,
                    'group_timer' => $section->group_timer ?? 0,
                    'shuffle_question' => $section->suffle_question,
                    'allow_back_to_group_question' => $section->allow_back_to_group_question,
                    'allow_back_to_previous_question' => $section->allow_back_to_previous_question,
                    'question_group' => $section->questionGroup,
                    'questions' => $section->assessmentQuestions()
                        ->orderBy('id')
                        ->get()
                        ->map(function ($aq) {
                            return [
                                'id' => $aq->id,
                                'assessment_question_id' => $aq->id,
                                'question_id' => $aq->question_id,
                                'question_content' => $aq->question->question_content,
                                'question_timer' => $aq->question_timer ?? 0,
                                'negative_mark' => $aq->negative_mark ?? 0,
                                'question_type_id' => $aq->question_type_id,
                                'answer_category' => $aq->question->answer_category,
                            ];
                        })
                        ->toArray(),
                ];
            })
            ->toArray();

        $this->setCurrentSection();
    }

    public function setDefaultLanguage()
    {
        $languages = ['en'];

        foreach ($this->sections as $section) {
            foreach ($section['questions'] as $question) {
                if (isset($question['question_content']['stem'])) {
                    $langs = array_keys($question['question_content']['stem']);
                    $languages = array_unique(array_merge($languages, $langs));
                }
            }
        }

        $this->availableLanguages = array_filter($languages, function ($lang) {
            return in_array($lang, ['as', 'bn', 'en', 'hn']);
        });

        if (!in_array('en', $this->availableLanguages)) {
            $this->language = $this->availableLanguages[0] ?? 'en';
        }
    }

    public function setCurrentSection()
    {
        if (isset($this->sections[$this->currentSectionIndex])) {
            $this->currentSection = $this->sections[$this->currentSectionIndex];
            $this->setCurrentQuestion();
        }
    }

    public function setCurrentQuestion()
    {
        if ($this->currentSection && isset($this->currentSection['questions'][$this->currentQuestionIndex])) {
            $this->currentQuestion = $this->currentSection['questions'][$this->currentQuestionIndex];
        }
    }

    public function changeLanguage($lang)
    {
        $this->language = $lang;
    }

    public function nextQuestion()
    {
        if ($this->currentSection) {
            if ($this->currentQuestionIndex < count($this->currentSection['questions']) - 1) {
                $this->currentQuestionIndex++;
            } elseif ($this->currentSectionIndex < count($this->sections) - 1) {
                $this->currentSectionIndex++;
                $this->currentQuestionIndex = 0;
            }

            $this->setCurrentSection();
            $this->setCurrentQuestion();
        }
    }

    public function previousQuestion()
    {
        if (!$this->currentSection['allow_back_to_previous_question']) {
            return;
        }

        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        } elseif ($this->currentSectionIndex > 0) {
            $this->currentSectionIndex--;
            $this->currentQuestionIndex = count($this->sections[$this->currentSectionIndex]['questions']) - 1;
        }

        $this->setCurrentSection();
        $this->setCurrentQuestion();
    }

    public function goToQuestion($sectionIndex, $questionIndex)
    {
        $this->currentSectionIndex = $sectionIndex;
        $this->currentQuestionIndex = $questionIndex;
        $this->setCurrentSection();
        $this->setCurrentQuestion();
    }

    public function toggleAnswered()
    {
        $key = $this->currentSectionIndex . '-' . $this->currentQuestionIndex;
        if (in_array($key, $this->answeredQuestions)) {
            $this->answeredQuestions = array_filter($this->answeredQuestions, fn($k) => $k !== $key);
        } else {
            $this->answeredQuestions[] = $key;
        }
    }

    public function isQuestionAnswered($sectionIndex, $questionIndex)
    {
        return in_array($sectionIndex . '-' . $questionIndex, $this->answeredQuestions);
    }

    public function isCurrentQuestion($sectionIndex, $questionIndex)
    {
        return $sectionIndex === $this->currentSectionIndex && $questionIndex === $this->currentQuestionIndex;
    }

    public function getText($content, $key = null)
    {
        if ($key) {
            $content = $content[$key] ?? null;
        }

        if (is_array($content)) {
            return $content[$this->language] ?? $content['en'] ?? '';
        }

        return $content ?? '';
    }

    public function render()
    {
        return view('livewire.admin.assessments.preview-assessment');
    }
}
