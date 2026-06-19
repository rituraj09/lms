<?php

namespace App\Models;


use Spatie\Permission\Models\Permission as SpatiePermission;

#[Unguarded]
class Permission extends SpatiePermission
{

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ─── Accessors ────────────────────────────────────────────────

    public function getDisplayNameAttribute($value): string
    {
        return $value ?? ucwords(str_replace(['.', '_'], ' ', $this->name));
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function getAllGrouped(): \Illuminate\Support\Collection
    {
        return self::where('guard_name', 'admin')
                   ->orderBy('group')
                   ->orderBy('sort_order')
                   ->orderBy('name')
                   ->get()
                   ->groupBy('group');
    }
}
