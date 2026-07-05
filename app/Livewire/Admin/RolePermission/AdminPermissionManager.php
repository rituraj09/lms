<?php

namespace App\Livewire\Admin\RolePermission;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use App\Services\ActivityLogger;
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

    public function mount(?string $id): void
    {
        $id = decrypt($id);

        $this->admin = Admin::findOrFail($id);

        $this->authorize('system.admin.assign_permissions');

        if (!auth('admin')->user()->canAssignPermissions($this->admin)) {
            abort(403, 'You cannot manage permissions for this admin.');
        }

        $this->loadCurrentPermissions();
    }

    public function loadCurrentPermissions()
    {
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
            'org_permissions'    => $this->orgPermissions,
            'selected_org'       => $this->selectedOrg,
        ]);
    }

    public function updatedSelectedOrg($orgId)
    {
        if (!$orgId) {
            $this->orgPermissions = [];
            return;
        }

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
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

        if ($this->admin->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            session()->flash('error', 'You cannot modify Super Admin permissions!');
            return;
        }

        try {
            $permissionId = (int) $permissionId;
            $permission   = Permission::findOrFail($permissionId);

            // Determine grant or revoke before making changes
            $isRevoking = in_array($permissionId, $this->systemPermissions, true);

            if ($isRevoking) {
                $this->admin->revokePermissionTo($permission);

                // Log: revoke system permission
                ActivityLogger::log(
                    userId:   auth('admin')->id(),
                    userType: 'admin',
                    action:   'revoke_permission',
                    extra: [
                        'model_type'  => 'Admin',
                        'model_id'    => $this->admin->id,
                        'description' => "Revoked system permission from admin: {$this->admin->name}",
                        'properties'  => [
                            'permission_id'   => $permission->id,
                            'permission_name' => $permission->name,
                            'display_name'    => $permission->display_name,
                            'target_admin_id' => $this->admin->id,
                            'target_admin'    => $this->admin->name,
                        ],
                    ]
                );

                session()->flash('success', "Permission '{$permission->display_name}' removed!");
            } else {
                $this->admin->givePermissionTo($permission);

                // Log: grant system permission
                ActivityLogger::log(
                    userId:   auth('admin')->id(),
                    userType: 'admin',
                    action:   'grant_permission',
                    extra: [
                        'model_type'  => 'Admin',
                        'model_id'    => $this->admin->id,
                        'description' => "Granted system permission '{$permission->display_name}' to admin: {$this->admin->name}",
                        'properties'  => [
                            'permission_id'   => $permission->id,
                            'permission_name' => $permission->name,
                            'display_name'    => $permission->display_name,
                            'target_admin_id' => $this->admin->id,
                            'target_admin'    => $this->admin->name,
                        ],
                    ]
                );

                session()->flash('success', "Permission '{$permission->display_name}' granted!");
            }

            $this->loadCurrentPermissions();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission: ' . $e->getMessage());
            \Log::error('Permission toggle failed', [
                'error'         => $e->getMessage(),
                'admin_id'      => $this->admin->id,
                'permission_id' => $permissionId ?? null,
            ]);
        }
    }

    /**
     * Toggle organisation mode permission
     */
    public function toggleOrgPermission($permissionId)
    {
        if ($this->admin->id === auth('admin')->id()) {
            session()->flash('error', 'You cannot modify your own permissions!');
            return;
        }

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
            $permission   = Permission::findOrFail($permissionId);

            // Determine grant or revoke before making changes
            $isRevoking = in_array($permission->name, $this->orgPermissions, true);

            if ($isRevoking) {
                $this->orgPermissions = array_filter(
                    $this->orgPermissions,
                    fn($p) => $p !== $permission->name
                );
            } else {
                $this->orgPermissions[] = $permission->name;
            }

            // Save to pivot table
            $this->admin->setOrgPermissions($this->selectedOrg, $this->orgPermissions);

            // Log: org permission toggle (grant or revoke)
            ActivityLogger::log(
                userId:   auth('admin')->id(),
                userType: 'admin',
                action:   $isRevoking ? 'revoke_permission' : 'grant_permission',
                extra: [
                    'model_type'  => 'Admin',
                    'model_id'    => $this->admin->id,
                    'description' => $isRevoking
                        ? "Revoked org permission '{$permission->display_name}' from admin: {$this->admin->name} for org ID: {$this->selectedOrg}"
                        : "Granted org permission '{$permission->display_name}' to admin: {$this->admin->name} for org ID: {$this->selectedOrg}",
                    'properties'  => [
                        'permission_id'   => $permission->id,
                        'permission_name' => $permission->name,
                        'display_name'    => $permission->display_name,
                        'organisation_id' => $this->selectedOrg,
                        'target_admin_id' => $this->admin->id,
                        'target_admin'    => $this->admin->name,
                    ],
                ]
            );

            session()->flash('success', "Permission '{$permission->display_name}' updated!");
            $this->loadCurrentPermissions();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission: ' . $e->getMessage());
            \Log::error('Org permission toggle failed', [
                'error'         => $e->getMessage(),
                'admin_id'      => $this->admin->id,
                'org_id'        => $this->selectedOrg,
                'permission_id' => $permissionId ?? null,
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

            // Capture permission names before detaching for the log
            $removedPermissionNames = Permission::whereIn('id', $this->systemPermissions)
                ->pluck('name')
                ->toArray();

            $this->admin->permissions()
                ->where('name', 'like', 'system.%')
                ->detach();

            DB::commit();

            // Log: bulk remove system permissions
            ActivityLogger::log(
                userId:   auth('admin')->id(),
                userType: 'admin',
                action:   'revoke_permissions',
                extra: [
                    'model_type'  => 'Admin',
                    'model_id'    => $this->admin->id,
                    'description' => "Removed all {$count} system permission(s) from admin: {$this->admin->name}",
                    'properties'  => [
                        'removed_count'       => $count,
                        'removed_permissions' => $removedPermissionNames,
                        'target_admin_id'     => $this->admin->id,
                        'target_admin'        => $this->admin->name,
                    ],
                ]
            );

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

            // Capture names before clearing for the log
            $removedPermissionNames = $this->orgPermissions;

            $this->admin->setOrgPermissions($this->selectedOrg, []);

            DB::commit();

            // Log: bulk remove org permissions
            ActivityLogger::log(
                userId:   auth('admin')->id(),
                userType: 'admin',
                action:   'revoke_permissions',
                extra: [
                    'model_type'  => 'Admin',
                    'model_id'    => $this->admin->id,
                    'description' => "Removed all {$count} organisation permission(s) from admin: {$this->admin->name} for org ID: {$this->selectedOrg}",
                    'properties'  => [
                        'removed_count'       => $count,
                        'removed_permissions' => $removedPermissionNames,
                        'organisation_id'     => $this->selectedOrg,
                        'target_admin_id'     => $this->admin->id,
                        'target_admin'        => $this->admin->name,
                    ],
                ]
            );

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
        $pattern = $this->mode === 'org' ? 'org.%' : 'system.%';

        $allPermissions = Permission::where('guard_name', 'admin')
            ->where('name', 'like', $pattern)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $permissions = $allPermissions;

        if ($this->selectedGroup !== 'all') {
            $permissions = collect([
                $this->selectedGroup => $permissions[$this->selectedGroup] ?? collect()
            ]);
        }

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
        $this->search        = '';
        $this->selectedGroup = 'all';
    }

    public function render()
    {
        return view('livewire.admin.role-permission.admin-permission-manager', [
            'roles'               => Role::where('guard_name', 'admin')->orderBy('name')->get(),
            'permissionGroups'    => $this->permissionGroups,
            'filteredPermissions' => $this->filteredPermissions,
            'totalPermissions'    => $this->totalPermissions,
        ]);
    }
}
