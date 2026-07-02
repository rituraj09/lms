<?php
// app/Models/Master/UserDetail.php

namespace App\Models\Master;

use App\Models\EvaluationMaster\AgeGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Unguarded]
class UserDetail extends Model
{
    protected $casts = [
        'date_of_birth' => 'date',
        'extra_data'  => 'array',
    ];

    // ─── Booted ───────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($detail) {
            if (empty($detail->student_id)) {
                $detail->student_id = self::generateStudentId($detail->user->organisation_id);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name)
            ?: $this->user->name;
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

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateStudentId(?int $organisationId = null): string
    {
        $orgPart = str_pad($organisationId ?? 0, 5, '0', STR_PAD_LEFT);
        $year = date('Y');

        // Get last serial number for this org and year
        $lastStudent = self::whereHas('user', function($q) use ($organisationId) {
            $q->where('organisation_id', $organisationId);
        })
            ->where('student_id', 'LIKE', "STUD-{$orgPart}-{$year}-%")
            ->orderByDesc('student_id')
            ->first();

        if ($lastStudent) {
            // Extract serial and increment
            $parts = explode('-', $lastStudent->student_id);
            $serial = intval($parts[3] ?? 0) + 1;
        } else {
            $serial = 1;
        }

        $serialPart = str_pad($serial, 5, '0', STR_PAD_LEFT);

        return "STUD-{$orgPart}-{$year}-{$serialPart}";
    }
    public function currentAgeGroup()
    {
        return $this->belongsTo(AgeGroup::class, 'current_age_group_id');
    }

    public function getPhysicalAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return Carbon::parse($this->date_of_birth)->age;
    }

}
