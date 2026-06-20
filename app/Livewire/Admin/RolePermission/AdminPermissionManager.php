<?php

namespace App\Livewire\Admin\RolePermission;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.backend')]
class AdminPermissionManager extends Component
{
    use AuthorizesRequests;

    public Admin $admin;
    public $adminId;
    public $selectedRole = null;
    public $rolePermissions = [];
    public $directPermissions = [];
    public $search = '';
    public $selectedGroup = 'all';

    protected $queryString = ['search', 'selectedGroup'];

    public function mount($adminId)
    {
        $this->adminId = $adminId;

        // ✅ Load with fresh relationships
        $this->admin = Admin::with([
            'roles.permissions',
            'permissions',
            'organisations'
        ])->findOrFail($adminId);

        // Authorization check
        $this->authorize('permission.manage');

        $this->loadCurrentPermissions();
    }

    public function loadCurrentPermissions()
    {
        // ✅ Refresh admin to get latest data
        $this->admin->refresh();
        $this->admin->load(['roles.permissions', 'permissions']);

        // Get current role
        $this->selectedRole = $this->admin->roles->first()?->id;

        // ✅ Get role permissions (via role) - ensure integers
        $this->rolePermissions = $this->admin->getPermissionsViaRoles()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        // ✅ Get direct permissions - ensure integers
        $this->directPermissions = $this->admin->permissions()
            ->pluck('permissions.id') // Specify table to avoid ambiguity
            ->map(fn($id) => (int) $id)
            ->toArray();

        // ✅ Debug log (remove in production)
        \Log::info('Loaded Permissions', [
            'role_permissions' => $this->rolePermissions,
            'direct_permissions' => $this->directPermissions,
        ]);
    }

    public function updatedSelectedRole($roleId)
    {
        if (!$roleId) {
            return;
        }

        // Prevent self-modification for critical roles
        if ($this->admin->id === auth('admin')->id() && $this->admin->isSuperAdmin()) {
            session()->flash('error', 'You cannot change your own Super Admin role!');
            $this->selectedRole = $this->admin->roles->first()?->id;
            return;
        }

        try {
            DB::beginTransaction();

            // Remove all current roles
            $this->admin->roles()->detach();

            // Assign new role
            $role = Role::findOrFail($roleId);
            $this->admin->assignRole($role);

            // Update current_role_id
            $this->admin->update(['current_role_id' => $roleId]);

            DB::commit();

            // ✅ Reload permissions
            $this->loadCurrentPermissions();

            session()->flash('success', "Role updated to '{$role->display_name}' successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update role: ' . $e->getMessage());

            \Log::error('Role update failed', [
                'error' => $e->getMessage(),
                'admin_id' => $this->admin->id,
                'role_id' => $roleId
            ]);
        }
    }

    public function toggleDirectPermission($permissionId)
    {
        // ✅ Ensure integer comparison
        $permissionId = (int) $permissionId;

        // Prevent self-modification
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        try {
            $permission = Permission::findOrFail($permissionId);

            if (in_array($permissionId, $this->directPermissions, true)) {
                // Remove direct permission
                $this->admin->revokePermissionTo($permission);

                session()->flash('success', "Permission '{$permission->display_name}' removed!");
            } else {
                // Add direct permission
                $this->admin->givePermissionTo($permission);

                session()->flash('success', "Permission '{$permission->display_name}' granted!");
            }

            // ✅ Reload permissions after change
            $this->loadCurrentPermissions();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission: ' . $e->getMessage());

            \Log::error('Permission toggle failed', [
                'error' => $e->getMessage(),
                'admin_id' => $this->admin->id,
                'permission_id' => $permissionId
            ]);
        }
    }

    public function removeAllDirectPermissions()
    {
        // Prevent self-modification
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        try {
            DB::beginTransaction();

            $count = count($this->directPermissions);

            // Remove all direct permissions (keep role permissions)
            $this->admin->permissions()->detach();

            DB::commit();

            // ✅ Reload permissions
            $this->loadCurrentPermissions();

            session()->flash('success', "Removed {$count} direct permission(s)!");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to remove permissions: ' . $e->getMessage());
        }
    }

    public function getFilteredPermissionsProperty()
    {
        $allPermissions = Permission::where('guard_name', 'admin')
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $permissions = $allPermissions;

        // Filter by group
        if ($this->selectedGroup !== 'all') {
            $permissions = collect([
                $this->selectedGroup => $permissions[$this->selectedGroup] ?? collect()
            ]);
        }

        // Filter by search
        if ($this->search) {
            $permissions = $permissions->map(function ($group) {
                return $group->filter(function ($permission) {
                    return str_contains(strtolower($permission->display_name), strtolower($this->search)) ||
                        str_contains(strtolower($permission->name), strtolower($this->search)) ||
                        str_contains(strtolower($permission->description ?? ''), strtolower($this->search));
                });
            })->filter(fn($group) => $group->isNotEmpty());
        }

        return $permissions;
    }

    public function getPermissionGroupsProperty()
    {
        return Permission::where('guard_name', 'admin')
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    public function getTotalPermissionsProperty()
    {
        return $this->admin->getAllPermissions()->count();
    }

    // ✅ Fixed: Ensure proper comparison with strict type checking
    public function hasPermission($permissionId)
    {
        $permissionId = (int) $permissionId;

        return in_array($permissionId, $this->rolePermissions, true) ||
            in_array($permissionId, $this->directPermissions, true);
    }

    // ✅ Fixed: Proper source detection
    public function getPermissionSource($permissionId)
    {
        $permissionId = (int) $permissionId;

        $viaRole = in_array($permissionId, $this->rolePermissions, true);
        $viaDirect = in_array($permissionId, $this->directPermissions, true);

        if ($viaRole && $viaDirect) return 'both';
        if ($viaRole) return 'role';
        if ($viaDirect) return 'direct';
        return 'none';
    }

    // ✅ New helper: Check if permission is directly assigned
    public function isDirectPermission($permissionId)
    {
        return in_array((int) $permissionId, $this->directPermissions, true);
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->selectedGroup = 'all';
    }

    public function render()
    {
        return view('livewire.admin.role-permission.admin-permission-manager', [
            'roles' => Role::where('guard_name', 'admin')->orderBy('name')->get(),
            'permissionGroups' => $this->permissionGroups,
            'filteredPermissions' => $this->filteredPermissions,
            'totalPermissions' => $this->totalPermissions,
        ]);
    }
}
