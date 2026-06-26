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

    public string $view = 'list';

    #[Locked]
    public ?int $assessmentId = null;

    public string $search       = '';
    public string $statusFilter = '';
    public string $typeFilter   = '';
    public int    $perPage      = 10;

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

    public array $assessmentGroups = [];

    public bool   $showGroupPicker    = false;
    public string $pickerMode         = 'new';
    public ?int   $pickerAgIndex      = null;
    public string $groupPickerSearch  = '';
    public ?int   $pickerGroupId      = null;
    public array  $pickerQuestions    = [];
    public array  $pickerSelectedQIds = [];

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

    protected array $languages = [];

    public $cover_image_file = null;
    public string $cover_image_path  = '';
    public bool $removeCoverImage    = false;

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
            ->withCount([
                'assessmentGroups',
                // ── NEW: Count all attempts ──────────────────────────
                'testAttempts as attempts_count',
                'testAttempts as in_progress_count' => fn($q) =>
                $q->where('status', 'in_progress'),
                'testAttempts as completed_count' => fn($q) =>
                $q->whereIn('status', ['submitted', 'evaluated']),
            ])
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

        // ── NEW: Block editing if has attempts ────────────────────
        if ($assessment->isLockedForEditing()) {
            session()->flash(
                'error',
                'This assessment cannot be edited because students have already attempted it. You can only change its status (Publish/Unpublish).'
            );
            return;
        }
        // ──────────────────────────────────────────────────────────

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
        $this->max_attempts          = (int)  $assessment->max_attempts;
        $this->shuffle_sections      = (bool) $assessment->shuffle_sections;
        $this->show_result_immediately = (bool) $assessment->show_result_immediately;
        $this->show_correct_answers  = (bool) $assessment->show_correct_answers;
        $this->show_explainations    = (bool) $assessment->show_explainations;
        $this->view = 'form';
    }

    public function openBuilder(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        // ── NEW: Block builder if has attempts ────────────────────
        if ($assessment->isLockedForEditing()) {
            session()->flash(
                'error',
                'This assessment is locked for editing because students have already attempted it.'
            );
            return;
        }
        // ──────────────────────────────────────────────────────────

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
        $this->max_attempts          = (int)  $assessment->max_attempts;
        $this->shuffle_sections      = (bool) $assessment->shuffle_sections;
        $this->show_result_immediately = (bool) $assessment->show_result_immediately;
        $this->show_correct_answers  = (bool) $assessment->show_correct_answers;
        $this->show_explainations    = (bool) $assessment->show_explainations;

        $this->loadBuilder($id);
        $this->view = 'builder';
    }

    public function deleteAssessment(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        // ── NEW: Block delete if has any attempts ─────────────────
        if ($assessment->hasAttempts()) {
            session()->flash(
                'error',
                'Cannot delete this assessment because students have already attempted it.'
            );
            return;
        }
        // ──────────────────────────────────────────────────────────

        if ($assessment->status === 'publish') {
            session()->flash('error', 'Cannot delete a published assessment.');
            return;
        }

        $assessment->delete();
        session()->flash('success', 'Assessment deleted successfully.');
    }

    /* ================================================================
     |  NEW: Change Status (Replaces toggleStatus)
     |  When assessment has attempts:
     |    - Only 'publish' and 'unpublish' are allowed
     |    - 'draft' is NOT allowed
     * ================================================================*/
    public function changeStatus(int $id, string $newStatus): void
    {
        // Validate status value
        if (! in_array($newStatus, ['draft', 'publish', 'unpublish'])) {
            session()->flash('error', 'Invalid status value.');
            return;
        }

        $assessment = Assessment::findOrFail($id);

        // ── LOCK RULE: If has attempts → only publish/unpublish ───
        if ($assessment->hasAttempts() && $newStatus === 'draft') {
            session()->flash(
                'error',
                'Cannot set to Draft because students have already attempted this assessment. Only Publish or Unpublish is allowed.'
            );
            return;
        }
        // ──────────────────────────────────────────────────────────

        // Prevent publishing if no questions exist
        if ($newStatus === 'publish') {
            $hasQuestions = $assessment->assessmentGroups()
                ->whereHas('assessmentQuestions')
                ->exists();

            if (! $hasQuestions) {
                session()->flash('error', 'Cannot publish an assessment with no questions.');
                return;
            }
        }

        $assessment->status     = $newStatus;
        $assessment->updated_by = auth()->id();
        $assessment->save();

        session()->flash('success', 'Assessment status changed to ' . ucfirst($newStatus) . '.');
    }

    /* ================================================================
     |  OLD toggleStatus — kept for backwards compatibility
     *  Now delegates to changeStatus
     * ================================================================*/
    public function toggleStatus(int $id): void
    {
        $assessment = Assessment::findOrFail($id);

        $newStatus = $assessment->status === 'publish'
            ? 'unpublish'
            : 'publish';

        $this->changeStatus($id, $newStatus);
    }

    /* ================================================================
     |  ASSESSMENT FORM — Save
     * ================================================================*/
    public function saveAssessment(): void
    {
        // ── NEW: Block save if assessment has attempts ─────────────
        if ($this->assessmentId) {
            $existing = Assessment::find($this->assessmentId);
            if ($existing && $existing->isLockedForEditing()) {
                session()->flash(
                    'error',
                    'Cannot edit this assessment because students have already attempted it.'
                );
                return;
            }
        }
        // ──────────────────────────────────────────────────────────

        $this->validate(
            [
                'title'                   => 'required|string|max:500',
                'cover_image_file'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'assessment_type_id'      => 'required|string',
                'age_group_id'            => 'required|integer',
                'passing_marks'           => 'required|numeric|min:0',
                'duration_minutes'        => 'required|numeric|min:0',
                'status'                  => 'required|in:draft,publish,unpublish',
                'max_attempts'            => 'required|integer|min:1',
                'shuffle_sections'        => 'boolean',
                'show_result_immediately' => 'boolean',
                'show_correct_answers'    => 'boolean',
                'show_explainations'      => 'boolean',
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

        // ── Handle Cover Image ─────────────────────────────────────
        $coverImagePath = $this->cover_image_path ?: null;

        if ($this->removeCoverImage) {
            if ($this->cover_image_path && Storage::disk('public')->exists($this->cover_image_path)) {
                Storage::disk('public')->delete($this->cover_image_path);
            }
            $coverImagePath = null;
        }

        if ($this->cover_image_file) {
            if ($this->cover_image_path && Storage::disk('public')->exists($this->cover_image_path)) {
                Storage::disk('public')->delete($this->cover_image_path);
            }
            $coverImagePath = $this->cover_image_file->store('assessments/covers', 'public');
        }

        try {
            $payload = [
                'assessment_code'         => $this->assessment_code,
                'title'                   => $this->title,
                'cover_image'             => $coverImagePath,
                'instructions'            => $this->instructions,
                'assessment_type_id'      => $this->assessment_type_id,
                'age_group_id'            => $this->age_group_id,
                'total_marks'             => $this->total_marks,
                'passing_marks'           => $this->passing_marks,
                'duration_minutes'        => !empty($this->duration_minutes)
                    ? (int) $this->duration_minutes
                    : null,
                'admin_note'              => $this->admin_note,
                'has_negative_mark'       => (bool) $this->has_negative_mark,
                'status'                  => $this->status,
                'max_attempts'            => (int)  $this->max_attempts,
                'shuffle_sections'        => (bool) $this->shuffle_sections,
                'show_result_immediately' => (bool) $this->show_result_immediately,
                'show_correct_answers'    => (bool) $this->show_correct_answers,
                'show_explainations'      => (bool) $this->show_explainations,
                'updated_by'              => auth()->id(),
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
     |  BUILDER — Save (Block if has attempts)
     * ================================================================*/
    public function saveBuilder(): void
    {
        // ── NEW: Block builder save if assessment has attempts ─────
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
        // ──────────────────────────────────────────────────────────

        if (! $this->assessmentId) {
            $this->addError('builder', 'Please save assessment info first.');
            return;
        }

        if (empty($this->assessmentGroups)) {
            $this->addError('builder', 'Please add at least one question group.');
            return;
        }

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

    // ... all other existing methods remain the same ...
    // (loadBuilder, openGroupPicker, closeGroupPicker, etc.)

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
        $this->max_attempts          = 1;
        $this->shuffle_sections      = false;
        $this->show_result_immediately = true;
        $this->show_correct_answers  = false;
        $this->show_explainations    = false;
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
