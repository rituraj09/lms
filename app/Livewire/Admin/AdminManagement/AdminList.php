<?php
// app/Livewire/Admin/AdminManagement/AdminList.php


namespace App\Livewire\Admin\AdminManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class AdminList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

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
        $this->dispatch('notify', type: 'success', message: 'Admin deleted successfully!');
    }

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
            'status' => $admin->status === 'active' ? 'inactive' : 'active'
        ]);

        $this->dispatch('notify', type: 'success', message: 'Admin status updated!');
    }

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

    public function render()
    {
        return view('livewire.admin.admin-management.admin-list', [
            'admins' => $this->admins,
        ]);
    }
}
