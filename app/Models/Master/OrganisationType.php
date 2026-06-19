<?php

// app/Models/Master/Organisation.php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Unguarded]
class OrganisationType extends Model
{

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ─── Booted ───────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($type) {
            if (empty($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    public function organisations()
    {
        return $this->hasMany(Organisation::class, 'organisation_type_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
