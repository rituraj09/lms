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

    // ── System Mode ────────────────────────────────────────────
    public $systemPermissions = [];
    public $allSystemPermissions = [];

    // ── Organisation Mode ──────────────────────────────────────
    public $selectedOrg = null;
    public $orgPermissions = [];
    public $allOrgPermissions = [];

    // ── Filter/Search ──────────────────────────────────────────
    public $search = '';
    public $selectedGroup = 'all';
    public $mode = 'system'; // 'system' or 'org'

    protected $queryString = ['search', 'selectedGroup', 'mode'];

    // ✅ FIXED: Accept Admin model directly from route binding
    public function mount(Admin $admin)
    {
        $this->admin = $admin;

        // Authorization check
        $this->authorize('system.admin.assign_permissions');

        // ✅ Additional check: Can this user manage the target admin?
        if (!auth('admin')->user()->canAssignPermissions($this->admin)) {
            abort(403, 'You cannot manage permissions for this admin.');
        }

        $this->loadCurrentPermissions();
    }

    public function loadCurrentPermissions()
    {
        // ✅ Refresh admin to get latest data
        $this->admin->refresh();
        $this->admin->load(['roles', 'permissions', 'organisations']);

        // ── System Mode Permissions ────────────────────────────
        $this->systemPermissions = $this->admin->getPermissionsViaRoles()
            ->filter(fn($p) => str_starts_with($p->name, 'system.'))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $directSystemPerms = $this->admin->permissions()
            ->where('name', 'like', 'system.%')
            ->pluck('permissions.id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $this->systemPermissions = array_unique(
            array_merge($this->systemPermissions, $directSystemPerms)
        );

        // ── Org Mode Permissions ───────────────────────────────
        if ($this->selectedOrg) {
            $this->orgPermissions = $this->admin->getOrgPermissions($this->selectedOrg);
        }

        \Log::info('Loaded Permissions', [
            'system_permissions' => $this->systemPermissions,
            'org_permissions' => $this->orgPermissions,
            'selected_org' => $this->selectedOrg,
        ]);
    }

    public function updatedSelectedOrg($orgId)
    {
        if (!$orgId) {
            $this->orgPermissions = [];
            return;
        }

        // ✅ Verify admin has access to this org
        if (!$this->admin->hasOrganisationAccess($orgId)) {
            session()->flash('error', 'This admin does not have access to this organisation.');
            $this->selectedOrg = null;
            return;
        }

        $this->loadCurrentPermissions();
    }

    public function updatedMode($newMode)
    {
        if (!in_array($newMode, ['system', 'org'])) {
            $this->mode = 'system';
        }
    }

    /**
     * Toggle system mode permission
     */
    public function toggleSystemPermission($permissionId)
    {
        // ✅ Prevent self-modification
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        // ✅ Prevent non-super-admin from assigning to super-admin
        if ($this->admin->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            session()->flash('error', 'You cannot modify Super Admin permissions!');
            return;
        }

        try {
            $permissionId = (int) $permissionId;
            $permission = Permission::findOrFail($permissionId);

            if (in_array($permissionId, $this->systemPermissions, true)) {
                $this->admin->revokePermissionTo($permission);
                session()->flash('success', "Permission '{$permission->display_name}' removed!");
            } else {
                $this->admin->givePermissionTo($permission);
                session()->flash('success', "Permission '{$permission->display_name}' granted!");
            }

            $this->loadCurrentPermissions();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission: ' . $e->getMessage());
            \Log::error('Permission toggle failed', [
                'error' => $e->getMessage(),
                'admin_id' => $this->admin->id,
                'permission_id' => $permissionId ?? null
            ]);
        }
    }

    /**
     * Toggle organisation mode permission
     */
    public function toggleOrgPermission($permissionId)
    {
        // ✅ Prevent self-modification
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        // ✅ Prevent non-super-admin from assigning to super-admin
        if ($this->admin->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            session()->flash('error', 'You cannot modify Super Admin permissions!');
            return;
        }

        if (!$this->selectedOrg) {
            session()->flash('error', 'Please select an organisation first.');
            return;
        }

        try {
            $permissionId = (int) $permissionId;
            $permission = Permission::findOrFail($permissionId);

            if (in_array($permission->name, $this->orgPermissions, true)) {
                // Remove from org permissions
                $this->orgPermissions = array_filter(
                    $this->orgPermissions,
                    fn($p) => $p !== $permission->name
                );
            } else {
                // Add to org permissions
                $this->orgPermissions[] = $permission->name;
            }

            // ✅ Save to pivot table
            $this->admin->setOrgPermissions($this->selectedOrg, $this->orgPermissions);

            session()->flash('success', "Permission '{$permission->display_name}' updated!");
            $this->loadCurrentPermissions();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission: ' . $e->getMessage());
            \Log::error('Org permission toggle failed', [
                'error' => $e->getMessage(),
                'admin_id' => $this->admin->id,
                'org_id' => $this->selectedOrg,
                'permission_id' => $permissionId ?? null
            ]);
        }
    }

    /**
     * Remove all direct system permissions
     */
    public function removeAllSystemPermissions()
    {
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        try {
            DB::beginTransaction();

            $count = count($this->systemPermissions);
            $this->admin->permissions()
                        ->where('name', 'like', 'system.%')
                        ->detach();

            DB::commit();

            $this->loadCurrentPermissions();
            session()->flash('success', "Removed {$count} system permission(s)!");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to remove permissions: ' . $e->getMessage());
        }
    }

    /**
     * Remove all org permissions for selected org
     */
    public function removeAllOrgPermissions()
    {
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        if (!$this->selectedOrg) {
            session()->flash('error', 'Please select an organisation first.');
            return;
        }

        try {
            DB::beginTransaction();

            $count = count($this->orgPermissions);
            $this->admin->setOrgPermissions($this->selectedOrg, []);

            DB::commit();

            $this->orgPermissions = [];
            session()->flash('success', "Removed {$count} organisation permission(s)!");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to remove permissions: ' . $e->getMessage());
        }
    }

    /**
     * Get filtered permissions based on search and group
     */
    public function getFilteredPermissionsProperty()
    {
        // Determine which permissions to show based on mode
        $pattern = $this->mode === 'org' ? 'org.%' : 'system.%';

        $allPermissions = Permission::where('guard_name', 'admin')
            ->where('name', 'like', $pattern)
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

    /**
     * Get permission groups for current mode
     */
    public function getPermissionGroupsProperty()
    {
        $pattern = $this->mode === 'org' ? 'org.%' : 'system.%';

        return Permission::where('guard_name', 'admin')
            ->where('name', 'like', $pattern)
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    /**
     * Get total permissions for current mode
     */
    public function getTotalPermissionsProperty()
    {
        if ($this->mode === 'org') {
            return count($this->orgPermissions);
        }
        return count($this->systemPermissions);
    }

    /**
     * Check if permission is assigned in current mode
     */
    public function hasPermission($permissionId)
    {
        $permissionId = (int) $permissionId;

        if ($this->mode === 'org') {
            $perm = Permission::find($permissionId);
            return $perm && in_array($perm->name, $this->orgPermissions, true);
        }

        return in_array($permissionId, $this->systemPermissions, true);
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
