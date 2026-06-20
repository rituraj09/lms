<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Master\AdminDetail;
use App\Models\Master\Organisation;
use App\Models\Role;  // ← Correct import - from App\Models
#[Unguarded]
class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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
                    ->withPivot('access_level')
                    ->withTimestamps();
    }

    public function studentTransfers()
    {
        return $this->hasMany(StudentTransfer::class, 'transferred_by');
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

    // ✅ FIXED - Type hint matches the import above
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

    // ─── Permission Helpers ───────────────────────────────────
    public function permissions()
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id'
        );
    }
    public function getRolePermissions(): \Illuminate\Support\Collection
    {
        return $this->getPermissionsViaRoles();
    }

    public function getDirectPermissions(): \Illuminate\Support\Collection
    {
        return $this->permissions;
    }

    public function getPermissionSource(string $permissionName): string
    {
        $hasViaRole = $this->getRolePermissions()->pluck('name')->contains($permissionName);
        $hasDirect  = $this->getDirectPermissions()->pluck('name')->contains($permissionName);

        if ($hasViaRole && $hasDirect) return 'both';
        if ($hasViaRole)               return 'role';
        if ($hasDirect)                return 'direct';
        return 'none';
    }

    // ─── Organisation Helpers ─────────────────────────────────

    public function hasOrganisationAccess(int $organisationId): bool
    {
        // ── Super admin bypasses org check ────────────────────────
        if ($this->isSuperAdmin()) {
            return true;
        }

        // ── Others must be explicitly assigned ────────────────────
        return $this->organisations()
                    ->where('organisation_id', $organisationId)
                    ->exists();
    }

    /**
     * Super admin can see ALL organisations.
     * Others only see their assigned ones.
     */
    public function getAccessibleOrganisations()
    {
        if ($this->isSuperAdmin()) {
            return \App\Models\Master\Organisation::active()->get();
        }

        return $this->organisations()->wherePivot('access_level', '!=', null)->get();
    }

    public function getSingleOrganisation(): ?Organisation
    {
        $orgs = $this->organisations;
        return $orgs->count() === 1 ? $orgs->first() : null;
    }

    public function setCurrentOrganisation(?int $organisationId): void
    {
        $this->update(['current_organisation_id' => $organisationId]);
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
}
