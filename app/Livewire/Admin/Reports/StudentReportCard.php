<?php

// app/Livewire/Admin/Reports/StudentReportCard.php
namespace App\Livewire\Admin\Reports;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\StudentReportService;
use App\Models\EvaluationMaster\AgeGroup;
use App\Models\User;

#[Layout('layouts.backend')]
class StudentReportCard extends Component
{
    use WithPagination;

    public $search = '';
    public $ageGroupFilter = '';
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'ageGroupFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAgeGroupFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with([
            'details.currentAgeGroup',
            'organisation',
            'completedTestAttempts.assessment',
            'completedTestAttempts.responses.assessmentQuestion.question.primarySkill',
            'completedTestAttempts.responses.assessmentQuestion.question.subSkill'
        ])
            ->whereHas('completedTestAttempts'); // Only users with completed attempts

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhereHas('details', function($ud) {
                        $ud->where('student_id', 'like', "%{$this->search}%")
                            ->orWhere('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
            });
        }

        // Age group filter
        if ($this->ageGroupFilter) {
            $query->whereHas('details', function($q) {
                $q->where('current_age_group_id', $this->ageGroupFilter);
            });
        }

        $users = $query->paginate($this->perPage);

        $ageGroups = AgeGroup::where('is_active', true)->get();

        return view('livewire.admin.reports.student-report-card', [
            'users' => $users,
            'ageGroups' => $ageGroups
        ]);
    }

    public function exportReports()
    {
        // Add export functionality later
        session()->flash('message', 'Export feature coming soon!');
    }
}
