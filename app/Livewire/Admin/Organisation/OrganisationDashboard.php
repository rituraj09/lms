<?php
// app/Livewire/Admin/Organisation/OrganisationDashboard.php


namespace App\Livewire\Admin\Organisation;

use Livewire\Component;
use App\Models\Master\Organisation;
use App\Services\OrganisationContext;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class OrganisationDashboard extends Component
{
    public Organisation $organisation;
    public int $studentCount = 0;
    public int $activeStudentCount = 0;
    public int $pendingStudentCount = 0;
    public int $availableSlots = 0;

    public function mount(int $orgId): void
    {
        $admin = auth('admin')->user();

        if (!$admin->hasOrganisationAccess($orgId)) {
            $this->redirect(route('admin.organisations.index'));
        }

        $this->organisation = Organisation::findOrFail($orgId);
        OrganisationContext::set($orgId);

        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $this->studentCount = $this->organisation->students()->count();
        $this->activeStudentCount = $this->organisation->activeStudents()->count();
        $this->pendingStudentCount = $this->organisation->students()->where('status', 'pending')->count();
        $this->availableSlots = $this->organisation->available_slots ?? 0;
    }

    public function render()
    {
        return view('livewire.admin.organisation.organisation-dashboard', [
            'organisation' => $this->organisation,
            'stats' => [
                'total_students' => $this->studentCount,
                'active_students' => $this->activeStudentCount,
                'pending_students' => $this->pendingStudentCount,
                'available_slots' => $this->availableSlots,
            ],
        ]);
    }
}
