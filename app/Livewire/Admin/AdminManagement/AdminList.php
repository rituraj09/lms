<?php
// app/Livewire/Admin/AdminManagement/AdminList.php

namespace App\Livewire\Admin\AdminManagement;

use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class AdminList extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $roleFilter   = '';
    public string $statusFilter = '';
    public int    $perPage      = 10;

    // ✅ Modal properties
    public bool  $showDetailModal = false;
    public ?int  $viewingAdminId  = null;

    protected $queryString = [
        'search'       => ['except' => ''],
        'roleFilter'   => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ─── Modal Methods ────────────────────────────────────────

    /**
     * Open detail modal
     */
    public function viewAdmin(int $id): void
    {
        $this->viewingAdminId  = $id;
        $this->showDetailModal = true;
    }

    /**
     * Close detail modal
     */
    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->viewingAdminId  = null;
    }

    // ─── Actions ──────────────────────────────────────────────

    /**
     * Toggle admin status
     */
    public function toggleStatus(int $id): void
    {
        $this->authorize('admin.edit');

        $admin = Admin::findOrFail($id);

        // Prevent changing own status
        if ($admin->id === auth('admin')->id()) {
            $this->dispatch('notify', type: 'error', message: 'You cannot change your own status!');
            return;
        }

        $admin->update([
            'status' => $admin->status === 'active' ? 'inactive' : 'active',
        ]);

        $this->dispatch('notify', type: 'success', message: 'Admin status updated!');
    }

    /**
     * Delete admin
     */
    public function deleteAdmin(int $id): void
    {
        $this->authorize('admin.delete');

        $admin = Admin::findOrFail($id);

        // Prevent deletion of super admins
        if ($admin->isSuperAdmin()) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete super admin!');
            return;
        }

        // Prevent self-deletion
        if ($admin->id === auth('admin')->id()) {
            $this->dispatch('notify', type: 'error', message: 'You cannot delete your own account!');
            return;
        }

        $admin->delete();
        ActivityLogger::log(
            userId:   auth('admin')->id(),
            userType: 'admin',
            action:   'delete',
            extra: [
                'model_type'  => 'Admin',
                'model_id'    => $admin->id,
                'description' => "Deleted admin: {$admin->name}",
                'properties'  => [

                    'email'         => $admin->email,
                    'mobile'        => $admin->mobile,
                ],
            ]
        );
        // Close modal if viewing the deleted admin
        if ($this->viewingAdminId === $id) {
            $this->closeDetailModal();
        }

        $this->dispatch('notify', type: 'success', message: 'Admin deleted successfully!');
    }

    // ─── Computed Properties ──────────────────────────────────

    /**
     * Get paginated admins list
     */
    public function getAdminsProperty()
    {
        return Admin::query()
            ->with(['roles', 'details', 'organisations'])
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('mobile', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    /**
     * Get the admin being viewed in modal
     */
    public function getViewingAdminProperty(): ?Admin
    {
        if (!$this->viewingAdminId) {
            return null;
        }

        return Admin::with([
                'roles',
                'details.state',
                'details.district',
                'organisations',
                'currentOrganisation'
            ])
            ->find($this->viewingAdminId);
    }

    // ─── Render ───────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.admin-management.admin-list', [
            'admins'       => $this->admins,
            'viewingAdmin' => $this->viewingAdmin,
        ]);
    }
}
