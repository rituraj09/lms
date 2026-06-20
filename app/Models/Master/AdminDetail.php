<?php

namespace App\Models\Master;

use App\Models\Admin;
use App\Models\Master\State;
use App\Models\Master\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Unguarded]
class AdminDetail extends Model
{

    protected $casts = [
        'date_of_birth' => 'date',
        'social_links'  => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name)
            ?: $this->admin->name;
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->district?->name,
            $this->state?->name,
            $this->postal_code,
            $this->country,
        ])->filter()->implode(', ');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
