<?php

namespace App\Livewire\Admin\Organisation;

use Livewire\Component;
use App\Models\Master\Organisation;
use App\Services\OrganisationContext;
use Livewire\Attributes\Layout;
use App\Traits\WithOrganisationAccess;

#[Layout('layouts.backend')]
class OrganisationDashboard extends Component
{
    use WithOrganisationAccess;

    public Organisation $organisation;
    public int $studentCount = 0;
    public int $activeStudentCount = 0;
    public int $pendingStudentCount = 0;
    public int $availableSlots = 0;

    public function mount(): void
    {
        // ✅ Get organisationId from route (works for both modes)
        $organisationId = request()->route('organisationId');

        if (!$organisationId) {
            abort(404, 'Organisation not found');
        }

        $orgId = (int) $organisationId;
        $admin = auth('admin')->user();

        if (!$admin) {
            redirect()->route('admin.login');
            return;
        }

        // ✅ Check organisation access
        if (!$admin->isSuperAdmin() && !$admin->hasOrganisationAccess($orgId)) {
            abort(403, 'You do not have access to this organisation');
        }

        // ✅ Check permission (works for both system and org mode)
        if (!$admin->isSuperAdmin()) {
            // For system mode: check system.organisation.view
            // For org mode: check org.dashboard.view
            $hasSystemPerm = $admin->hasSystemPermission('system.organisation.view');
            $hasOrgPerm = $admin->hasOrgPermission('org.dashboard.view', $orgId);

            if (!$hasSystemPerm && !$hasOrgPerm) {
                abort(403, 'You do not have permission to view this dashboard');
            }
        }

        // Find organisation
        $this->organisation = Organisation::findOrFail($orgId);

        // Set organisation context
        OrganisationContext::set($orgId);
        $admin->setCurrentOrganisation($orgId);

        // Load statistics
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $this->studentCount = $this->organisation->students()->count();
        $this->activeStudentCount = $this->organisation->activeStudents()->count();
        $this->pendingStudentCount = $this->organisation->students()
                                                        ->where('status', 'pending')
                                                        ->count();
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
