<?php

namespace App\Models\Master;

use App\Models\Admin;
use App\Models\User;
use App\Models\Master\State;
use App\Models\Master\District;
use App\Models\Master\OrganisationType;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Unguarded]
class Organisation extends Model
{
    use SoftDeletes;

    protected $casts = [
        'settings'           => 'array',
        'subscription_start' => 'date',
        'subscription_end'   => 'date',
        'max_students'       => 'integer',
    ];

    // ─── Booted ───────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($org) {
            // Auto slug
            if (empty($org->slug)) {
                $org->slug = self::generateUniqueSlug-($org->name);
            }
            // Auto code
            if (empty($org->code)) {
                $org->code = self::generateUniqueCode();
            }
        });

        static::updating(function ($org) {
            if ($org->isDirty('name') && empty($org->slug)) {
                $org->slug = self::generateUniqueSlug($org->name);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_organisation')
                    ->withPivot('access_level')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(User::class, 'organisation_id');
    }

    public function activeStudents()
    {
        return $this->hasMany(User::class, 'organisation_id')
                    ->where('status', 'active');
    }

    public function transfersIn()
    {
        return $this->hasMany(StudentTransfer::class, 'to_organisation_id');
    }

    public function transfersOut()
    {
        return $this->hasMany(StudentTransfer::class, 'from_organisation_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOfType($query, int $typeId)
    {
        return $query->where('organisation_type_id', $typeId);
    }

    public function scopeInState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    public function scopeInDistrict($query, int $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('assets/images/default-org.png');
    }

    public function getBannerUrlAttribute(): string
    {
        return $this->banner
            ? asset('storage/' . $this->banner)
            : asset('assets/images/default-banner.png');
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

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsSubscriptionActiveAttribute(): bool
    {
        if (!$this->subscription_end) return true;
        return $this->subscription_end->isFuture();
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getHasAvailableSlotsAttribute(): bool
    {
        if ($this->max_students === 0) return true;
        return $this->student_count < $this->max_students;
    }

    public function getAvailableSlotsAttribute(): ?int
    {
        if ($this->max_students === 0) return null; // unlimited
        return max(0, $this->max_students - $this->student_count);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = self::where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\AssessmentMaster\Assessment::class,
            'assessment_organisation'
        )
            ->withPivot([
                'assigned_by',
                'status',
                'assigned_date',
                'expiry_date',
                'assignment_note',
            ])
            ->withTimestamps();
    }
    public function activeAssessments(): BelongsToMany
    {
        return $this->assessments()
            ->wherePivot('status', 'active')
            ->where('assessments.status', 'public');
    }
}

