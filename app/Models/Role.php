<?php

namespace App\Models;


use Spatie\Permission\Models\Role as SpatieRole;

#[Unguarded]
class Role extends SpatieRole
{


    protected $casts = [
        'is_system' => 'boolean',
    ];

    const SYSTEM_ROLES = ['super_admin', 'admin', 'master_trainer'];

    // ─── Helpers ──────────────────────────────────────────────────

    public function isSystemRole(): bool
    {
        return $this->is_system || in_array($this->name, self::SYSTEM_ROLES);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getDisplayNameAttribute($value): string
    {
        return $value ?? ucwords(str_replace('_', ' ', $this->name));
    }

    public function getAdminCountAttribute(): int
    {
        return Admin::role($this->name)->count();
    }

    public function getGroupedPermissionsAttribute(): \Illuminate\Support\Collection
    {
        return $this->permissions->groupBy('group')->sortKeys();
    }
}
