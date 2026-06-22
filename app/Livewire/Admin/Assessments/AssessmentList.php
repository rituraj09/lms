<?php
// app/Livewire/Admin/Assessments/AssessmentList.php

namespace App\Livewire\Admin\Assessments;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AssessmentMaster\Assessment;
use Illuminate\Support\Facades\Gate;
#[Layout('layouts.backend')]
class AssessmentList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $ageGroupFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'ageGroupFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingAgeGroupFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $assessments = Assessment::query()
        ->where('status', 'publish')
            ->with([
                'ageGroup',
                'createdBy',
                'organisations' => function ($query) {
                    $query->where('assessment_organisation.status', 'active');
                }

            ])

            ->withCount([
                'assessmentGroups as total_questions' => function ($q) {
                    $q->join('assessment_questions', 'assessment_groups.id', '=', 'assessment_questions.assessment_group_id');
                }
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('assessment_code', 'like', '%' . $this->search . '%')
                        ->orWhere('title', 'like', '%' . $this->search . '%')
                        ->orWhere('admin_note', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })

            ->when($this->ageGroupFilter, function ($query) {
                $query->where('age_group_id', $this->ageGroupFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        $ageGroups = \App\Models\EvaluationMaster\AgeGroup::orderBy('name')->get();

        return view('livewire.admin.assessments.assessment-list', [
            'assessments' => $assessments,
            'ageGroups' => $ageGroups,
        ]);
    }
}
