<?php
// app/Livewire/Admin/Assessments/AssignAssessment.php

namespace App\Livewire\Admin\Assessments;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AssessmentMaster\Assessment;
use App\Models\Master\Organisation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Crypt;

#[Layout('layouts.backend')]
class AssignAssessment extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Assessment $assessment;
    public $search = '';
    public $perPage = 15;
    public $statusFilter = 'active';
    public $typeFilter = '';
    public $stateFilter = '';

    public $selectedOrganisations = [];
    public $selectAll = false;

    public $assignmentNote = '';
    public $expiryDate = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'active'],
    ];

    public function mount($encryptedId)
    {
        abort_unless(Gate::allows('system.assessment.assign'), 403);

        try {
            // Decrypt the ID
            $assessmentId = Crypt::decrypt($encryptedId);

            // Load the assessment
            $this->assessment = Assessment::with(['ageGroup', 'createdBy'])
                ->findOrFail($assessmentId);

            // Load already assigned organisations
            $this->selectedOrganisations = $this->assessment->organisations()
                ->wherePivot('status', 'active')
                ->pluck('organisations.id')
                ->toArray();

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid assessment identifier');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Assessment not found');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrganisations = $this->getFilteredOrganisations()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedOrganisations = [];
        }
    }

    public function toggleOrganisation($organisationId)
    {
        if (in_array($organisationId, $this->selectedOrganisations)) {
            $this->selectedOrganisations = array_diff($this->selectedOrganisations, [$organisationId]);
        } else {
            $this->selectedOrganisations[] = $organisationId;
        }

        $this->selectAll = false;
    }

    public function saveAssignments()
    {
        abort_unless(Gate::allows('system.assessment.assign'), 403);

        $this->validate([
            'selectedOrganisations' => 'required|array|min:1',
            'selectedOrganisations.*' => 'exists:organisations,id',
            'expiryDate' => 'nullable|date|after:today',
        ], [
            'selectedOrganisations.required' => 'Please select at least one organisation.',
            'selectedOrganisations.min' => 'Please select at least one organisation.',
        ]);

        try {
            DB::beginTransaction();

            $syncData = [];

            foreach ($this->selectedOrganisations as $orgId) {
                $syncData[$orgId] = [
                    'assigned_by' => auth()->id(),
                    'status' => 'active',
                    'assigned_date' => now(),
                    'expiry_date' => $this->expiryDate,
                    'assignment_note' => $this->assignmentNote,
                    'updated_at' => now(),
                ];
            }

            // Sync will automatically remove unselected and add new ones
            $this->assessment->organisations()->sync($syncData);

            DB::commit();

            session()->flash('success', 'Assessment assigned successfully to ' . count($this->selectedOrganisations) . ' organisation(s).');

            return redirect()->route('admin.assessments.assessment_list');

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Failed to assign assessment: ' . $e->getMessage());
        }
    }

    public function removeAssignment($organisationId)
    {
        abort_unless(Gate::allows('system.assessment.assign'), 403);

        try {
            $this->assessment->organisations()->detach($organisationId);

            $this->selectedOrganisations = array_diff($this->selectedOrganisations, [$organisationId]);

            session()->flash('success', 'Organisation removed from assignment.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove assignment.');
        }
    }

    private function getFilteredOrganisations()
    {
        return Organisation::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('organisation_type_id', $this->typeFilter);
            })
            ->when($this->stateFilter, function ($query) {
                $query->where('state_id', $this->stateFilter);
            })
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        $organisations = Organisation::query()
            ->with(['state', 'district', 'organisationType'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('organisation_type_id', $this->typeFilter);
            })
            ->when($this->stateFilter, function ($query) {
                $query->where('state_id', $this->stateFilter);
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        $organisationTypes = \App\Models\Master\OrganisationType::orderBy('name')->get();
        $states = \App\Models\Master\State::orderBy('name')->get();

        return view('livewire.admin.assessments.assign-assessment', [
                'assessment' => $this->assessment,
            'organisations' => $organisations,
            'organisationTypes' => $organisationTypes,
            'states' => $states,
        ]);
    }
}
