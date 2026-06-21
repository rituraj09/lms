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

    // View state
    public $viewMode = 'list'; // 'list', 'create', 'edit', 'view'

    // Form properties
    public $roleId = null;
    public $name = '';
    public $display_name = '';
    public $description = '';
    public $color = '#6c757d';
    public $icon = 'ri ri-shield-line';
    public $is_system = false;

    // Permission properties
    public $selectedSystemPermissions = [];
    public $selectedOrgPermissions = [];
    public $allSystemPermissions = [];
    public $allOrgPermissions = [];
    public $permissionTab = 'system';

    public function mount()
    {
        $this->authorize('system.role.view');
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        // System permissions
        $this->allSystemPermissions = Permission::where('guard_name', 'admin')
            ->where('name', 'like', 'system.%')
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->toArray();

        // Org permissions
        $this->allOrgPermissions = Permission::where('guard_name', 'admin')
            ->where('name', 'like', 'org.%')
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->toArray();
    }

    // ══════════════════════════════════════════════════════════
    // View Mode Methods
    // ══════════════════════════════════════════════════════════

    public function showCreateForm()
    {
        $this->authorize('system.role.create');
        $this->resetForm();
        $this->viewMode = 'create';
    }

    public function showEditForm($roleId)
    {
        $this->authorize('system.role.edit');

        $role = Role::with('permissions')->findOrFail($roleId);

        // ✅ Prevent editing system roles (super_admin)
        if ($role->is_system) {
            session()->flash('error', 'System roles cannot be edited!');
            return;
        }

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description;
        $this->color = $role->color ?? '#6c757d';
        $this->icon = $role->icon ?? 'ri ri-shield-line';
        $this->is_system = $role->is_system ?? false;

        // Load existing permissions split by mode
        $this->selectedSystemPermissions = $role->permissions
            ->filter(fn($p) => str_starts_with($p->name, 'system.'))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $this->selectedOrgPermissions = $role->permissions
            ->filter(fn($p) => str_starts_with($p->name, 'org.'))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $this->viewMode = 'edit';
    }

    public function showViewDetails($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description;
        $this->color = $role->color ?? '#6c757d';
        $this->icon = $role->icon ?? 'ri ri-shield-line';
        $this->is_system = $role->is_system ?? false;

        $this->selectedSystemPermissions = $role->permissions
            ->filter(fn($p) => str_starts_with($p->name, 'system.'))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $this->selectedOrgPermissions = $role->permissions
            ->filter(fn($p) => str_starts_with($p->name, 'org.'))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $this->viewMode = 'view';
    }

    public function backToList()
    {
        $this->resetForm();
        $this->viewMode = 'list';
    }

    // ══════════════════════════════════════════════════════════
    // CRUD Methods
    // ══════════════════════════════════════════════════════════

    public function createRole()
    {
        $this->authorize('system.role.create');

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

            // Sync both system and org permissions
            $allSelected = array_merge(
                $this->selectedSystemPermissions,
                $this->selectedOrgPermissions
            );

            if (!empty($allSelected)) {
                $role->syncPermissions(
                    Permission::whereIn('id', $allSelected)->get()
                );
            }

            DB::commit();

            session()->flash('success', "Role '{$role->display_name}' created successfully!");
            $this->backToList();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    public function updateRole()
    {
        $this->authorize('system.role.edit');

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

            // Sync both system and org permissions
            $allSelected = array_merge(
                $this->selectedSystemPermissions,
                $this->selectedOrgPermissions
            );

            $role->syncPermissions(
                Permission::whereIn('id', $allSelected)->get()
            );

            DB::commit();

            session()->flash('success', "Role '{$role->display_name}' updated successfully!");
            $this->backToList();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function deleteRole($roleId)
    {
        $this->authorize('system.role.delete');

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

    // ══════════════════════════════════════════════════════════
    // Permission Helpers
    // ══════════════════════════════════════════════════════════

    public function selectAllSystemPermissions()
    {
        $this->selectedSystemPermissions = Permission::where('guard_name', 'admin')
            ->where('name', 'like', 'system.%')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();
    }

    public function deselectAllSystemPermissions()
    {
        $this->selectedSystemPermissions = [];
    }

    public function selectAllOrgPermissions()
    {
        $this->selectedOrgPermissions = Permission::where('guard_name', 'admin')
            ->where('name', 'like', 'org.%')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();
    }

    public function deselectAllOrgPermissions()
    {
        $this->selectedOrgPermissions = [];
    }

    public function selectAllPermissions()
    {
        $this->selectAllSystemPermissions();
        $this->selectAllOrgPermissions();
    }

    public function deselectAllPermissions()
    {
        $this->selectedSystemPermissions = [];
        $this->selectedOrgPermissions = [];
    }

    public function getTotalSelectedProperty()
    {
        return count($this->selectedSystemPermissions) + count($this->selectedOrgPermissions);
    }

    // ══════════════════════════════════════════════════════════
    // Form Helpers
    // ══════════════════════════════════════════════════════════

    public function resetForm()
    {
        $this->roleId = null;
        $this->name = '';
        $this->display_name = '';
        $this->description = '';
        $this->color = '#6c757d';
        $this->icon = 'ri ri-shield-line';
        $this->is_system = false;
        $this->selectedSystemPermissions = [];
        $this->selectedOrgPermissions = [];
        $this->permissionTab = 'system';
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════════════════════════
    // Query
    // ══════════════════════════════════════════════════════════

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
            'roles' => $this->viewMode === 'list' ? $this->roles : collect(),
            'allSystemPermissions' => $this->allSystemPermissions,
            'allOrgPermissions' => $this->allOrgPermissions,
            'totalSelected' => $this->totalSelected,
        ]);
    }
}
