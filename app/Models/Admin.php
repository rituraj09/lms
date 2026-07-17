<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Master\AdminDetail;
use App\Models\Master\Organisation;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;

#[Unguarded]
class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    protected $guard_name = 'admin';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function details()
    {
        return $this->hasOne(AdminDetail::class);
    }

    public function currentOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'current_organisation_id');
    }

    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, 'admin_organisation')
                    ->withPivot('access_level', 'org_permissions', 'assigned_by', 'assigned_at')
                    ->withTimestamps();
    }

    public function studentTransfers()
    {
        return $this->hasMany(StudentTransfer::class, 'transferred_by');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Admin::class, 'assigned_by');
    }

    // ─── Role Helpers ─────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isMasterTrainer(): bool
    {
        return $this->hasRole('master_trainer');
    }

    public function getPrimaryRole(): ?Role
    {
        return $this->roles()->first();
    }

    public function getPrimaryRoleName(): string
    {
        $role = $this->getPrimaryRole();
        if (!$role) {
            return 'No Role';
        }
        return $role->display_name ?? $role->name ?? 'No Role';
    }

    public function getPrimaryRoleColor(): string
    {
        $role = $this->getPrimaryRole();
        return $role?->color ?? '#6c757d';
    }

    public function getPrimaryRoleIcon(): string
    {
        $role = $this->getPrimaryRole();
        return $role?->icon ?? 'fas fa-user';
    }

    // ─── Permission Helpers (SYSTEM MODE) ──────────────────────

    /**
     * Get all system mode permissions (system.*)
     * Super admin gets all, others get only direct permissions
     */
    public function getSystemPermissions(): Collection
    {
        if ($this->isSuperAdmin()) {
            return Permission::where('name', 'like', 'system.%')->get();
        }

        return $this->getAllPermissions()
                    ->filter(fn($p) => str_starts_with($p->name, 'system.'));
    }

    /**
     * Get system mode permission names only
     */
    public function getSystemPermissionNames(): array
    {
        return $this->getSystemPermissions()->pluck('name')->toArray();
    }

    /**
     * Check if has system mode permission
     */
    public function hasSystemPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->hasPermissionTo($permission);
    }

    // ─── Permission Helpers (ORGANISATION MODE) ───────────────

    /**
     * Get all organisation mode permissions for a specific org
     * Super admin gets all, others get from pivot table
     */
    public function getOrgPermissions(int $organisationId): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::where('name', 'like', 'org.%')
                            ->pluck('name')
                            ->toArray();
        }

        $pivot = $this->organisations()
                      ->where('organisation_id', $organisationId)
                      ->first();

        if (!$pivot || !$pivot->pivot->org_permissions) {
            return [];
        }

        return json_decode($pivot->pivot->org_permissions, true) ?? [];
    }

    /**
     * Check if has organisation mode permission
     */
    public function hasOrgPermission(string $permission, int $organisationId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $orgPerms = $this->getOrgPermissions($organisationId);
        return in_array($permission, $orgPerms);
    }

    /**
     * Set organisation permissions (only by super_admin)
     */
    public function setOrgPermissions(int $organisationId, array $permissions): void
    {
        $this->organisations()->syncWithoutDetaching([
            $organisationId => [
                'org_permissions' => json_encode($permissions),
                'assigned_by' => auth('admin')->id(),
                'assigned_at' => now(),
            ]
        ]);
    }

    // ─── Context-Aware Permission Checking ────────────────────

    /**
     * Check permission based on current mode
     */
    public function hasPermissionInContext(string $permission): bool
    {
        // Super admin bypasses all checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        // System mode permission
        if (str_starts_with($permission, 'system.')) {
            return $this->hasSystemPermission($permission);
        }

        // Org mode permission - check current org context
        if (str_starts_with($permission, 'org.')) {
            if (!$this->current_organisation_id) {
                return false;
            }
            return $this->hasOrgPermission($permission, $this->current_organisation_id);
        }

        return false;
    }

    // ─── Admin Management Protection ───────────────────────────

    /**
     * Check if this user can manage another admin
     * Rule: Nobody can touch super_admin except super_admin themselves
     */
    public function canManageAdmin(Admin $targetAdmin): bool
    {
        // Super admin can manage everyone
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Non-super admins CANNOT manage super_admin
        if ($targetAdmin->isSuperAdmin()) {
            return false;
        }

        // Check if user has permission to manage admins
        return $this->hasSystemPermission('system.admin.edit');
    }

    /**
     * Check if user can view super admin in lists
     */
    public function canViewSuperAdmin(): bool
    {
        return $this->isSuperAdmin() ||
               $this->hasSystemPermission('system.admin.view_super_admin');
    }

    /**
     * Check if user can assign permissions to another admin
     */
    public function canAssignPermissions(Admin $targetAdmin): bool
    {
        // Super admin can assign to everyone
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Non-super admins cannot assign permissions
        return false;
    }

    // ─── Organisation Helpers ─────────────────────────────────

    /**
     * ✅ NEW: Get single organisation if user has only one
     */
    public function getSingleOrganisation(): ?Organisation
    {
        $orgs = $this->organisations;
        return $orgs->count() === 1 ? $orgs->first() : null;
    }

    public function hasOrganisationAccess(int $organisationId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->organisations()
                    ->where('organisation_id', $organisationId)
                    ->exists();
    }

    public function getAccessibleOrganisations()
    {
        if ($this->isSuperAdmin()) {
            return Organisation::active()->get();
        }

        return $this->organisations;
    }

    public function setCurrentOrganisation(?int $organisationId): void
    {
        $this->update(['current_organisation_id' => $organisationId]);
    }

    public function isInOrgMode(): bool
    {
        return !is_null($this->current_organisation_id);
    }

    public function isInSystemMode(): bool
    {
        return is_null($this->current_organisation_id);
    }

    public function getCurrentMode(): string
    {
        return $this->isInOrgMode() ? 'organisation' : 'system';
    }

    // ─── Accessors ────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('assets/images/default-avatar.png');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->details) {
            $full = trim(
                ($this->details->first_name ?? '') . ' ' .
                ($this->details->last_name ?? '')
            );
            return $full ?: $this->name;
        }
        return $this->name;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Exclude super admins from query
     */
    public function scopeExcludeSuperAdmin($query)
    {
        return $query->whereDoesntHave('roles', function($q) {
            $q->where('name', 'super_admin');
        });
    }

    /**
     * Show only admins that current user can manage
     */
    public function scopeManageableBy($query, Admin $user)
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->excludeSuperAdmin();
    }
}
