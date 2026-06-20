<?php
// app/Livewire/Admin/Organisation/OrganisationList.php


namespace App\Livewire\Admin\Organisation;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Master\Organisation;
use App\Services\OrganisationContext;
use Livewire\Attributes\Layout;


#[Layout('layouts.backend')]
class OrganisationList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function enterOrganisation(int $id): void
    {
        $admin = auth('admin')->user();

        if (!$admin->hasOrganisationAccess($id)) {
            $this->dispatch('notify', type: 'error', message: 'Access Denied!');
            return;
        }

        OrganisationContext::set($id);
        $this->redirect(route('admin.organisation.dashboard', $id), navigate: true);
    }

    public function deleteOrganisation(int $id): void
    {
        $this->authorize('organisation.delete');

        $org = Organisation::findOrFail($id);
        $org->delete();

        $this->dispatch('notify', type: 'success', message: 'Organisation deleted successfully!');
    }


    public function getOrganisationsProperty()
    {
        $admin = auth('admin')->user();

        $query = Organisation::query()
            ->with(['state', 'district', 'organisationType'])
            ->withCount('students')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->when($this->typeFilter, fn($q) =>
                $q->where('organisation_type_id', $this->typeFilter)
            )
            ->orderBy($this->sortBy, $this->sortDir);

        // ── Super admin sees ALL orgs ──────────────────────────────
        if (!$admin->isSuperAdmin()) {
            $query->whereHas('admins', fn($q) =>
                $q->where('admin_id', $admin->id)
            );
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.organisation.organisation-list', [
            'organisations' => $this->organisations,
            'organisationTypes' => \App\Models\Master\OrganisationType::active()->get(),
        ]);
    }
}
