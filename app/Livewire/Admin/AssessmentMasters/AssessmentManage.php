<?php

namespace App\Livewire\Admin\AssessmentMasters;

use App\Models\EvaluationMaster\DifficultyLevel;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\AssessmentMaster\Assessment;
use App\Models\EvaluationMaster\AgeGroup;
use App\Helper\Globals;

#[Layout('layouts.backend')]
class AssessmentManage extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $assessmentId = null;

    public string $assessment_code    = '';
    public string $title              = '';
    public string $instructions       = '';
    public string $assessment_type_id = '';
    public ?int   $age_group_id       = null;
    public ?int   $difficulty_level_id       = null;

    public float  $total_marks        = 0;
    public float  $passing_marks      = 0;
    public ?int   $duration_minutes   = null;
    public string $admin_note         = '';
    public bool   $has_negative_mark  = false;
    public string $status             = 'draft';

    public $cover_image_file         = null;
    public string $cover_image_path  = '';
    public bool $removeCoverImage    = false;

    public int  $max_attempts            = 1;
    public bool $shuffle_sections        = false;
    public bool $show_result_immediately = true;
    public bool $show_correct_answers    = false;
    public bool $show_explainations      = false;

    public array $ageGroups       = [];
    public array $difficultyLevels       = [];
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
     |  MOUNT
     * ================================================================*/
    public function mount(?string $id = null): void
    {
        $this->ageGroups = AgeGroup::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        $this->difficultyLevels = DifficultyLevel::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        if ($id) {
            $decryptedId = decrypt($id);
            $assessment  = Assessment::findOrFail($decryptedId);

            if ($assessment->isLockedForEditing()) {
                session()->flash(
                    'error',
                    'This assessment cannot be edited because students have already attempted it.'
                );
                $this->redirect(route('admin.assessment-masters.list'), navigate: false);
                return;
            }

            $this->assessmentId            = $decryptedId;  // ← Now assigning int
            $this->assessment_code         = $assessment->assessment_code;
            $this->title                   = $assessment->title;
            $this->instructions            = $assessment->instructions ?? '';
            $this->assessment_type_id      = $assessment->assessment_type_id;
            $this->age_group_id            = $assessment->age_group_id;
            $this->difficulty_level_id     = $assessment->difficulty_level_id;
            $this->total_marks             = (float) $assessment->total_marks;
            $this->passing_marks           = (float) $assessment->passing_marks;
            $this->duration_minutes        = $assessment->duration_minutes;
            $this->admin_note              = $assessment->admin_note ?? '';
            $this->has_negative_mark       = (bool) $assessment->has_negative_mark;
            $this->status                  = $assessment->status;
            $this->cover_image_path        = $assessment->cover_image ?? '';
            $this->cover_image_file        = null;
            $this->removeCoverImage        = false;
            $this->max_attempts            = (int)  $assessment->max_attempts;
            $this->shuffle_sections        = (bool) $assessment->shuffle_sections;
            $this->show_result_immediately = (bool) $assessment->show_result_immediately;
            $this->show_correct_answers    = (bool) $assessment->show_correct_answers;
            $this->show_explainations      = (bool) $assessment->show_explainations;
        } else {
            $this->assessment_code = $this->generateAssessmentCode();
        }
    }

    /* ================================================================
     |  SAVE ASSESSMENT
     * ================================================================*/
    public function saveAssessment(): void
    {
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

        $this->validate(
            [
                'title'                   => 'required|string|max:500',
                'cover_image_file'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'assessment_type_id'      => 'required|string',
                'age_group_id'            => 'required|integer',
                'difficulty_level_id'     => 'required|integer',
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
                'difficulty_level_id.required' => 'Please select an difficulty level.',
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
                'difficulty_level_id'     => $this->difficulty_level_id,
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

            $this->redirect(
                route('admin.assessment-masters.build', ['id' => encrypt($assessment->id)]),
               navigate: false
            );

        } catch (\Throwable $e) {
            Log::error('saveAssessment failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            $this->addError('save_failed', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function removeCoverImageFile(): void
    {
        $this->removeCoverImage = true;
        $this->cover_image_file = null;
        $this->cover_image_path = '';
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

    /* ================================================================
     |  RENDER
     * ================================================================*/
    public function render()
    {
        return view('livewire.admin.assessment-masters.assessment-manage');
    }
}
