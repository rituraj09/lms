<?php

namespace App\Livewire\EvaluationMaster;

use App\Helper\Code;
use App\Helper\Globals;
use App\Models\EvaluationMaster\AgeGroup;
use App\Models\EvaluationMaster\QuestionSet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.backend')]
class QuestionSetManager extends Component
{
    use WithFileUploads;

    // ─── View State ──────────────────────────────────────────
    // 0 = datatable list, 1 = create/edit set details form
    public int  $view      = 0;
    public bool $isEditing = false;

    // ─── Form Fields ─────────────────────────────────────────
    public ?int   $setId            = null;
    public string $code             = '';
    public string $title            = '';
    public string $slug             = '';
    public string $questionSetType  = '';
    public string $description      = '';
    public ?int   $ageGroupId       = null;
    public ?int   $timer            = null;
    public ?int   $passingScore     = null;
    public bool   $randomizeQuestions = false;
    public string $status           = 'draft';

    // ─── Media ───────────────────────────────────────────────
    public mixed   $imageUpload = null;
    public ?string $imagePath   = null;

    // ─── Reference Data ──────────────────────────────────────
    public array $ageGroups = [];

    public const SET_TYPES = [
        'iq' => 'IQ — Intelligence Quotient',
        'eq' => 'EQ — Emotional Quotient',
        'lq' => 'LQ — Leadership Quotient',
    ];

    // =========================================================
    // LIFECYCLE
    // =========================================================

    public function mount(): void
    {
        $this->ageGroups = AgeGroup::select('id', 'name')->get()->toArray();
    }

    // =========================================================
    // VALIDATION
    // =========================================================

    protected function rules(): array
    {
        $uniqueCode  = 'unique:question_sets,code,'  . ($this->setId ?? 'NULL');
        $uniqueTitle = 'unique:question_sets,title,'  . ($this->setId ?? 'NULL');
        $uniqueSlug  = 'unique:question_sets,slug,'   . ($this->setId ?? 'NULL');

        return [
            'title'            => "required|string|max:200|{$uniqueTitle}",
            'questionSetType'  => 'required|in:iq,eq,lq',
            'ageGroupId'       => 'required|exists:age_groups,id',
            'description'      => 'nullable|string',
            'timer'            => 'nullable|integer|min:1|max:65535',
            'passingScore'     => 'nullable|integer|min:0',
            'randomizeQuestions' => 'boolean',
            'status'           => 'required|in:draft,publish,unpublish',
            'imageUpload'      => 'nullable|image|mimes:jpeg,png,gif,webp|max:4096',
        ];
    }

    protected array $messages = [
        'title.required'           => 'Question set title is required.',
        'questionSetType.required' => 'Please select a set type.',
        'ageGroupId.required'      => 'Please select an age group.',
    ];

    // =========================================================
    // WATCHERS
    // =========================================================

    public function updatedTitle(string $value): void
    {
        // Auto-generate slug from title while creating
        if (! $this->isEditing) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedImageUpload(): void
    {
        $this->validateOnly('imageUpload');
    }

    // =========================================================
    // MEDIA
    // =========================================================

    public function removeImageUpload(): void
    {
        $this->imageUpload = null;
        $this->resetErrorBag('imageUpload');
    }

    public function removeImagePath(): void
    {
        if ($this->imagePath) {
            Storage::disk('public')->delete($this->imagePath);
        }
        $this->imagePath = null;
    }

    // =========================================================
    // VIEW NAVIGATION
    // =========================================================

    #[On('new-entry')]
    public function newEntry(): void
    {
        $this->resetForm();
        $this->code   = Code::generateQuestionCode(Auth::guard('admin')->id());
        $this->status = 'draft';
        $this->view   = 1;
    }

    #[On('edit')]
    public function edit(int $id): void
    {
        $this->resetForm();
        $this->loadSet($id);
        $this->view = 1;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->view = 0;
    }

    // =========================================================
    // PERSISTENCE — Step 1
    // =========================================================

    /**
     * Save the set details and redirect to the builder (Step 2 + 3).
     */
    public function saveAndContinue()
    {
        $this->validate();

        // Handle image
        if ($this->imageUpload) {
            if ($this->imagePath) {
                Storage::disk('public')->delete($this->imagePath);
            }
            $this->imagePath   = $this->imageUpload->store('question-sets', 'public');
            $this->imageUpload = null;
        }

        $set = QuestionSet::updateOrCreate(
            ['id' => $this->setId],
            [
                'code'                => $this->code,
                'title'               => $this->title,
                'slug'                => Str::slug($this->slug ?: $this->title),
                'question_set_type'   => $this->questionSetType,
                'description'         => $this->description,
                'age_group_id'        => $this->ageGroupId,
                'image_path'          => $this->imagePath,
                'timer'               => $this->timer,
                'passing_score'       => $this->passingScore,
                'randomize_questions' => $this->randomizeQuestions,
                'status'              => $this->status,
                'created_by'          => $this->setId ? $set->created_by ?? Auth::guard('admin')->id() : Auth::guard('admin')->id(),
                'updated_by'          => Auth::guard('admin')->id(),
            ]
        );

        // Redirect to the builder component (separate page / route)
        // $this->redirect(
        //     route('admin.question-sets.builder', ['setId' => $set->id]),
        //     navigate: true
        // );

        return $this->redirectRoute(
            'admin.question-sets.builder',
            ['setId' => $set->id]
        );
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        return view('livewire.evaluation-master.question-set.manager');
    }

    // =========================================================
    // PRIVATE
    // =========================================================

    private function resetForm(): void
    {
        $this->reset([
            'setId', 'code', 'title', 'slug', 'questionSetType', 'description',
            'ageGroupId', 'timer', 'passingScore', 'randomizeQuestions', 'status',
            'imageUpload', 'imagePath',
        ]);
        $this->isEditing = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    private function loadSet(int $id): void
    {
        $set = QuestionSet::findOrFail($id);

        $this->setId              = $set->id;
        $this->code               = $set->code;
        $this->title              = $set->title;
        $this->slug               = $set->slug;
        $this->questionSetType    = $set->question_set_type;
        $this->description        = $set->description ?? '';
        $this->ageGroupId         = $set->age_group_id;
        $this->imagePath          = $set->image_path;
        $this->timer              = $set->timer;
        $this->passingScore       = $set->passing_score;
        $this->randomizeQuestions = $set->randomize_questions;
        $this->status             = $set->status;
        $this->isEditing          = true;
    }
}
