<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\User;
use App\Models\Master\Organisation;
use Illuminate\Support\Facades\Crypt;

#[Layout('layouts.backend')]
class StudentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $organisationFilter = '';
    public int $perPage = 25;
    public string $sortBy = 'organisation_id';
    public string $sortDir = 'asc';
    public array $selectedRows = [];
    public bool $selectAll = false;

    public array $perPageOptions = [10, 25, 50, 100];
    public array $searchableColumns = ['name', 'email', 'phone', 'student ID'];
    public string $emptyMessage = 'No students found';
    public string $title = 'All Students';
    public bool $exportable = true;
    public bool $newEntry = false;

    protected $queryString = [
        'search'              => ['except' => ''],
        'sortBy'              => ['except' => 'organisation_id'],
        'sortDir'             => ['except' => 'asc'],
        'perPage'             => ['except' => 25],
        'statusFilter'        => ['except' => ''],
        'organisationFilter'  => ['except' => ''],
    ];

    public function mount()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOrganisationFilter(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function setFilter(string $key, mixed $value): void
    {
        $this->$key = $value;
        $this->resetPage();
    }

    public function clearFilter(string $key): void
    {
        $this->$key = '';
        $this->resetPage();
    }

    public function clearAllFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'organisationFilter']);
        $this->resetPage();
    }

    public function dispatchAction(string $event, int $id): mixed
    {
        if ($event === 'view-student') {
            $student = User::find($id);

            if ($student && $student->organisation_id) {
                return redirect()->route('admin.org.students.view', [
                    'organisationId' => Crypt::encrypt($student->organisation_id),
                    'studentId'      => Crypt::encrypt($id),
                ]);
            }
        }

        return null;
    }

    public function export(string $format = 'csv'): void
    {
        // TODO: implement export
        $this->dispatch('notify', [
            'message' => 'Export coming soon!',
            'type'    => 'info',
        ]);
    }

    #[Computed]
    public function rows()
    {
        return User::with(['details', 'organisation'])
            ->whereHas('details')
            ->whereNotNull('organisation_id')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhereHas('details', fn($d) =>
                        $d->where('student_id', 'like', '%' . $this->search . '%')
                        );
                });
            })
            ->when($this->statusFilter, fn($q) =>
            $q->where('status', $this->statusFilter)
            )
            ->when($this->organisationFilter, fn($q) =>
            $q->where('organisation_id', $this->organisationFilter)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function organisations()
    {
        return Organisation::orderBy('name')->get();
    }

    #[Computed]
    public function activeFilters(): array
    {
        return collect([
            'statusFilter'       => $this->statusFilter,
            'organisationFilter' => $this->organisationFilter,
        ])->filter()->toArray();
    }

    #[Computed]
    public function filters(): array
    {
        return [
            [
                'key'     => 'statusFilter',
                'label'   => 'Status',
                'options' => [
                    ['value' => '',           'label' => 'All Status'],
                    ['value' => 'active',     'label' => 'Active'],
                    ['value' => 'inactive',   'label' => 'Inactive'],
                    ['value' => 'suspended',  'label' => 'Suspended'],
                    ['value' => 'pending',    'label' => 'Pending'],
                ],
            ],
            [
                'key'     => 'organisationFilter',
                'label'   => 'Organisation',
                'options' => $this->organisations
                    ->map(fn($org) => ['value' => $org->id, 'label' => $org->name])
                    ->prepend(['value' => '', 'label' => 'All Organisations'])
                    ->toArray(),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports.student-list');
    }
}
