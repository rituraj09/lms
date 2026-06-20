<?php

namespace App\Livewire\Admin\RolePermission;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class RoleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDir = 'asc';
    public $perPage = 10;

    // Modal state - ✅ Simple boolean
    public $isCreateModalOpen = false;
    public $isEditModalOpen = false;
    public $isViewModalOpen = false;

    // Form properties
    public $roleId = null;
    public $name = '';
    public $display_name = '';
    public $description = '';
    public $color = '#6c757d';
    public $icon = 'ri-shield-line';
    public $is_system = false;
    public $selectedPermissions = [];
    public $allPermissions = [];

    public function mount()
    {
        $this->authorize('role.view');
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $this->allPermissions = Permission::where('guard_name', 'admin')
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->toArray();
    }

    // ✅ SIMPLE MODAL METHODS
    public function openCreateModal()
    {
        $this->authorize('role.create');
        $this->resetForm();
        $this->isCreateModalOpen = true;
    }

    public function closeCreateModal()
    {
        $this->isCreateModalOpen = false;
        $this->resetForm();
    }

    public function openEditModal($roleId)
    {
        $this->authorize('role.edit');

        $role = Role::findOrFail($roleId);

        if ($role->is_system) {
            session()->flash('error', 'System roles cannot be edited!');
            return;
        }

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description;
        $this->color = $role->color ?? '#6c757d';
        $this->icon = $role->icon ?? 'ri-shield-line';
        $this->is_system = $role->is_system ?? false;
        $this->selectedPermissions = $role->permissions()->pluck('id')->toArray();

        $this->isEditModalOpen = true;
    }

    public function closeEditModal()
    {
        $this->isEditModalOpen = false;
        $this->resetForm();
    }

    public function openViewModal($roleId)
    {
        $role = Role::findOrFail($roleId);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description;
        $this->color = $role->color ?? '#6c757d';
        $this->icon = $role->icon ?? 'ri-shield-line';
        $this->is_system = $role->is_system ?? false;
        $this->selectedPermissions = $role->permissions()->pluck('id')->toArray();

        $this->isViewModalOpen = true;
    }

    public function closeViewModal()
    {
        $this->isViewModalOpen = false;
        $this->resetForm();
    }

    public function createRole()
    {
        $this->authorize('role.create');

        $this->validate([
            'name' => 'required|string|min:3|max:255|unique:roles,name,NULL,id,guard_name,admin',
            'display_name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string',
            'icon' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => strtolower(str_replace(' ', '_', $this->name)),
                'display_name' => $this->display_name,
                'description' => $this->description,
                'color' => $this->color,
                'icon' => $this->icon,
                'guard_name' => 'admin',
                'is_system' => false,
            ]);

            if (!empty($this->selectedPermissions)) {
                $role->syncPermissions(
                    Permission::whereIn('id', $this->selectedPermissions)->get()
                );
            }

            DB::commit();

            session()->flash('success', "Role '{$role->display_name}' created successfully!");

            $this->closeCreateModal();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    public function updateRole()
    {
        $this->authorize('role.edit');

        $role = Role::findOrFail($this->roleId);

        if ($role->is_system) {
            session()->flash('error', 'System roles cannot be edited!');
            return;
        }

        $this->validate([
            'display_name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string',
            'icon' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $role->update([
                'display_name' => $this->display_name,
                'description' => $this->description,
                'color' => $this->color,
                'icon' => $this->icon,
            ]);

            if (!empty($this->selectedPermissions)) {
                $role->syncPermissions(
                    Permission::whereIn('id', $this->selectedPermissions)->get()
                );
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            session()->flash('success', "Role '{$role->display_name}' updated successfully!");
            $this->closeEditModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function deleteRole($roleId)
    {
        $this->authorize('role.delete');

        $role = Role::findOrFail($roleId);

        if ($role->is_system) {
            session()->flash('error', 'System roles cannot be deleted!');
            return;
        }

        $adminCount = $role->users()->count();
        if ($adminCount > 0) {
            session()->flash('error', "Cannot delete role '{$role->display_name}'. It is assigned to {$adminCount} admin(s).");
            return;
        }

        try {
            DB::beginTransaction();

            $role->permissions()->detach();
            $role->delete();

            DB::commit();

            session()->flash('success', "Role '{$role->display_name}' deleted successfully!");
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    public function togglePermission($permissionId)
    {
        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionId]);
        } else {
            $this->selectedPermissions[] = $permissionId;
        }
    }

    public function selectAllPermissions()
    {
        $this->selectedPermissions = Permission::where('guard_name', 'admin')
            ->pluck('id')
            ->toArray();
    }

    public function deselectAllPermissions()
    {
        $this->selectedPermissions = [];
    }

    public function resetForm()
    {
        $this->roleId = null;
        $this->name = '';
        $this->display_name = '';
        $this->description = '';
        $this->color = '#6c757d';
        $this->icon = 'ri-shield-line';
        $this->is_system = false;
        $this->selectedPermissions = [];
        $this->resetErrorBag();
    }

    public function getRolesProperty()
    {
        $query = Role::where('guard_name', 'admin');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('display_name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return $query
            ->withCount('users')
            ->withCount('permissions')
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.role-permission.role-manager', [
            'roles' => $this->roles,
            'allPermissions' => $this->allPermissions,
        ]);
    }
}
