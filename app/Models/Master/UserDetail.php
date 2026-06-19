<?php

// app/Models/Master/UserDetail.php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
                $detail->student_id = self::generateStudentId();
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

    public static function generateStudentId(): string
    {
        $prefix = 'STU';
        $year   = date('Y');

        do {
            $random = strtoupper(Str::random(6));
            $id     = "{$prefix}{$year}{$random}";
        } while (self::where('student_id', $id)->exists());

        return $id;
    }
}
