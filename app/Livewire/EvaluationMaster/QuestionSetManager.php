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
use App\Models\EvaluationMaster\Question;
use App\Models\EvaluationMaster\QuestionSetGroup;
use App\Models\EvaluationMaster\QuestionQuestionSetGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;


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

    public function backtoQuestionset(): void
    {
        $this->loadSet($this->setId);
        $this->view = 1;
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
        $existingSet = $this->setId
            ? QuestionSet::find($this->setId)
            : null;

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
        $this->loadQuestionSet($set->id);// Pre-load the set for the builder

        // return $this->redirectRoute(
        //     'admin.question-sets.builder',
        //     ['setId' => $set->id]
        // );
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        return view('livewire.evaluation-master.question-set-manager');
    }

    // =========================================================
    // PRIVATE
    // =========================================================

    private function resetForm(): void
    {
        $this->reset([
            'setId', 'code', 'title', 'slug', 'questionSetType',  'description',
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
        $this->description        = $set->description;
        $this->ageGroupId         = $set->age_group_id;
        $this->imagePath          = $set->image_path;
        $this->timer              = $set->timer;
        $this->passingScore       = $set->passing_score;
        $this->randomizeQuestions = $set->randomize_questions;
        $this->status             = $set->status;
        $this->isEditing          = true;
    }


    //Question Set Builder
     use WithPagination;
 // ─── Set context ─────────────────────────────────────────

    public string $setTitle    = '';
    public string $setCode     = '';
    public string $setType     = '';
    public string $setStatus   = 'draft';
    public int    $totalQuestions = 0;

    // ─── Groups (loaded from DB, kept as lightweight array) ──
    // Each element: ['id' => int, 'title' => str, 'question_category' => str,
    //  'randomize_questions' => bool, 'allow_main_backtrack' => bool,
    //  'allow_backtrack' => bool, 'main_timer' => bool, 'question_count' => int]
    public array $groups = [];

    // ─── Group form (inline edit) ────────────────────────────
    public ?int   $editingGroupId       = null;   // null = creating new
    public string $groupTitle           = '';
    public string $groupCategory        = 'optional';
    public string $groupInstructions    = '';
    public bool   $groupRandomize       = false;
    public bool   $groupAllowMainBack   = true;
    public bool   $groupAllowBack       = true;
    public bool   $groupMainTimer       = false;
    public bool   $showGroupForm        = false;

    // ─── Question picker modal ────────────────────────────────
    public bool   $showPicker           = false;
    public ?int   $pickerGroupId        = null;   // group receiving questions
    public string $pickerSearch         = '';
    public string $pickerTypeFilter     = '';
    public array  $pickerSelected       = [];     // question IDs checked in modal
    public array  $alreadyInGroup       = [];     // IDs already assigned (grayed out)

    // ─── Question order / settings per group ─────────────────
    // [ groupId => [ questionId => ['order'=>int, 'timer'=>int|null,
    //                'score_override'=>float|null, 'negative_mark'=>float] ] ]
    public array $groupQuestionSettings = [];

    // ─── Reference data ──────────────────────────────────────
    public array $questionTypes = [];

    // =========================================================
    // LIFECYCLE
    // =========================================================

    public function loadQuestionSet(int $setId): void
    {
        $this->view = 2;
        $set = QuestionSet::with('groups.questions')->findOrFail($setId);

        $this->setId          = $set->id;
        $this->setTitle       = $set->title;
        $this->setCode        = $set->code;
        $this->setType        = $set->question_set_type;
        $this->setStatus      = $set->status;
        $this->totalQuestions = $set->total_questions;

        $this->loadGroups($set);

        $this->questionTypes = \App\Models\EvaluationMaster\QuestionType::select('id', 'name')
            ->get()->toArray();
    }

    // =========================================================
    // COMPUTED
    // =========================================================

    #[Computed]
    public function questionSet(): QuestionSet
    {
        return QuestionSet::findOrFail($this->setId);
    }

    /**
     * Questions for the picker modal — paginated, filtered,
     * excluding already-assigned questions.
     */
    #[Computed]
    public function pickerQuestions()
    {
        if (! $this->showPicker) return collect();

        return Question::query()
            ->when($this->pickerSearch, fn ($q) =>
                $q->where('code', 'like', "%{$this->pickerSearch}%")
            )
            ->when($this->pickerTypeFilter, fn ($q) =>
                $q->where('question_type_id', $this->pickerTypeFilter)
            )
            ->whereNotIn('id', $this->alreadyInGroup)
            ->where('status', 'publish')
            ->select('id', 'code', 'question_type_id', 'question_contents', 'max_score')
            ->latest()
            ->paginate(12, pageName: 'picker_page');
    }

    // =========================================================
    // GROUP MANAGEMENT (Step 2)
    // =========================================================

    public function openNewGroup(): void
    {
        $this->resetGroupForm();
        $this->showGroupForm = true;
    }

    public function openEditGroup(int $groupId): void
    {
        $group = QuestionSetGroup::findOrFail($groupId);

        $this->editingGroupId     = $group->id;
        $this->groupTitle         = $group->title;
        $this->groupCategory      = $group->question_category;
        $this->groupInstructions  = $group->instructions ?? '';
        $this->groupRandomize     = $group->randomize_questions;
        $this->groupAllowMainBack = $group->allow_main_backtrack;
        $this->groupAllowBack     = $group->allow_backtrack;
        $this->groupMainTimer     = $group->main_timer;
        $this->showGroupForm      = true;
    }

    public function saveGroup(): void
    {
        $this->validate([
            'groupTitle'    => 'required|string|max:255',
            'groupCategory' => 'required|in:optional,follow-up question,open-text',
        ], [
            'groupTitle.required'    => 'Group title is required.',
            'groupCategory.required' => 'Please select a question category.',
        ]);

        QuestionSetGroup::updateOrCreate(
            ['id' => $this->editingGroupId],
            [
                'question_set_id'       => $this->setId,
                'title'                 => $this->groupTitle,
                'question_category'     => $this->groupCategory,
                'instructions'          => $this->groupInstructions ?: null,
                'randomize_questions'   => $this->groupRandomize,
                'allow_main_backtrack'  => $this->groupAllowMainBack,
                'allow_backtrack'       => $this->groupAllowBack,
                'main_timer'            => $this->groupMainTimer,
                'created_by'            => Auth::guard('admin')->id(),
                'updated_by'            => Auth::guard('admin')->id(),
            ]
        );

        $this->resetGroupForm();
        $this->reloadGroups();
        session()->flash('success', 'Group saved.');
    }

    public function deleteGroup(int $groupId): void
    {
        $group = QuestionSetGroup::findOrFail($groupId);
        // Remove pivot rows first
        QuestionQuestionSetGroup::where('question_set_group_id', $groupId)->delete();
        $group->delete();

        $this->reloadGroups();
        $this->recountTotalQuestions();
        session()->flash('success', 'Group deleted.');
    }

    public function cancelGroupForm(): void
    {
        $this->resetGroupForm();
    }

    // =========================================================
    // QUESTION PICKER (Step 3)
    // =========================================================

    public function openPicker(int $groupId): void
    {
        $this->pickerGroupId  = $groupId;
        $this->pickerSearch   = '';
        $this->pickerTypeFilter = '';
        $this->pickerSelected = [];

        // Collect IDs already in this group so we can gray them out
        $this->alreadyInGroup = QuestionQuestionSetGroup::where('question_set_group_id', $groupId)
            ->where('status', 'active')
            ->pluck('question_id')
            ->toArray();

        $this->showPicker = true;
        $this->resetPage('picker_page');
    }

    public function closePicker(): void
    {
        $this->showPicker     = false;
        $this->pickerGroupId  = null;
        $this->pickerSelected = [];
    }

    public function togglePickerQuestion(int $questionId): void
    {
        if (in_array($questionId, $this->pickerSelected)) {
            $this->pickerSelected = array_values(
                array_filter($this->pickerSelected, fn ($id) => $id !== $questionId)
            );
        } else {
            $this->pickerSelected[] = $questionId;
        }
    }

    public function addSelectedToGroup(): void
    {
        if (! $this->pickerGroupId || empty($this->pickerSelected)) {
            $this->closePicker();
            return;
        }

        DB::transaction(function () {

            $maxOrder = QuestionQuestionSetGroup::where(
                'question_set_group_id',
                $this->pickerGroupId
            )->max('order') ?? 0;

            foreach ($this->pickerSelected as $questionId) {

                $pivot = QuestionQuestionSetGroup::withTrashed()
                    ->where('question_set_group_id', $this->pickerGroupId)
                    ->where('question_id', $questionId)
                    ->first();

                // Record already exists
                if ($pivot) {

                    // Restore if soft deleted
                    if ($pivot->trashed()) {

                        $pivot->restore();

                        $pivot->update([
                            'order'         => ++$maxOrder,
                            'negative_mark' => 0,
                            'status'        => 'active',
                        ]);
                    }

                    // Already active, skip
                    continue;
                }

                // Brand new record
                QuestionQuestionSetGroup::create([
                    'question_set_group_id' => $this->pickerGroupId,
                    'question_id'           => $questionId,
                    'order'                 => ++$maxOrder,
                    'negative_mark'         => 0,
                    'status'                => 'active',
                ]);
            }
        });

        $this->closePicker();
        $this->reloadGroups();
        $this->recountTotalQuestions();
    }

    // =========================================================
    // QUESTION SETTINGS (inline per assigned question)
    // =========================================================

    public function removeQuestionFromGroup(int $groupId, int $questionId): void
    {
        QuestionQuestionSetGroup::where('question_set_group_id', $groupId)
            ->where('question_id', $questionId)
            ->delete();

        $this->reloadGroups();
        $this->recountTotalQuestions();
    }

    public function updateQuestionSetting(int $groupId, int $questionId, string $field, mixed $value): void
    {
        $allowed = ['order', 'score_override', 'timer', 'negative_mark', 'status'];
        if (! in_array($field, $allowed)) return;

        QuestionQuestionSetGroup::where('question_set_group_id', $groupId)
            ->where('question_id', $questionId)
            ->update([$field => $value ?: null]);
    }

    public function moveQuestionUp(int $groupId, int $questionId): void
    {
        $this->swapOrder($groupId, $questionId, 'up');
    }

    public function moveQuestionDown(int $groupId, int $questionId): void
    {
        $this->swapOrder($groupId, $questionId, 'down');
    }

    // =========================================================
    // PUBLISH QUICK-TOGGLE
    // =========================================================

    public function updateSetStatus(string $status): void
    {
        QuestionSet::where('id', $this->setId)->update(['status' => $status]);
        $this->setStatus = $status;
    }


    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function loadGroups(QuestionSet $set): void
    {
        $this->groups = $set->groups()
            ->withCount([
                'questions as question_count' => fn ($query) =>
                    $query->where('question_question_set_groups.status', 'active')
            ])
            ->get()
            ->map(fn ($g) => [
                'id'                    => $g->id,
                'title'                 => $g->title,
                'instructions'           => $g->instructions,
                'question_category'     => $g->question_category,
                'randomize_questions'   => $g->randomize_questions,
                'allow_main_backtrack'  => $g->allow_main_backtrack,
                'allow_backtrack'       => $g->allow_backtrack,
                'main_timer'            => $g->main_timer,
                'question_count'        => $g->question_count,
            ])
            ->toArray();
    }

    private function reloadGroups(): void
    {
        $set = QuestionSet::with('groups')->find($this->setId);
        $this->loadGroups($set);
    }

    private function recountTotalQuestions(): void
    {
        $count = QuestionQuestionSetGroup::whereHas('group', fn ($q) =>
            $q->where('question_set_id', $this->setId)
        )->where('status', 'active')->count();

        QuestionSet::where('id', $this->setId)->update(['total_questions' => $count]);
        $this->totalQuestions = $count;
    }

    private function resetGroupForm(): void
    {
        $this->editingGroupId     = null;
        $this->groupTitle         = '';
        $this->groupCategory      = 'optional';
        $this->groupInstructions  = '';
        $this->groupRandomize     = false;
        $this->groupAllowMainBack = true;
        $this->groupAllowBack     = true;
        $this->groupMainTimer     = false;
        $this->showGroupForm      = false;
        $this->resetErrorBag(['groupTitle', 'groupCategory']);
    }

    private function swapOrder(int $groupId, int $questionId, string $direction): void
    {
        $rows = QuestionQuestionSetGroup::where('question_set_group_id', $groupId)
            ->orderBy('order')
            ->get();

        $index = $rows->search(fn ($r) => $r->question_id === $questionId);

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapIndex < 0 || $swapIndex >= $rows->count()) return;

        [$rows[$index]->order, $rows[$swapIndex]->order] = [$rows[$swapIndex]->order, $rows[$index]->order];
        $rows[$index]->save();
        $rows[$swapIndex]->save();

        $this->reloadGroups();
    }
}
