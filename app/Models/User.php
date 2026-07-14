<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Models\Master\UserDetail;
use App\Models\Master\Organisation;
use App\Models\TestAttempt\TestAttempt;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TestAttempt\UserPromotionDetail;

 #[Unguarded]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
     // ─── Relationships ────────────────────────────────────────────

    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function transferHistory()
    {
        return $this->hasMany(StudentTransfer::class, 'user_id')
                    ->orderByDesc('transferred_at');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForOrganisation($query, int $organisationId)
    {
        return $query->where('organisation_id', $organisationId);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('assets/images/default-avatar.png');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->details) {
            $full = trim($this->details->first_name . ' ' . $this->details->last_name);
            return $full ?: $this->name;
        }
        return $this->name;
    }

    public function getStudentIdAttribute(): ?string
    {
        return $this->details?->student_id;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class, 'user_id');
    }
     public function completedTestAttempts()
     {
         return $this->hasMany(TestAttempt::class)->where('status', 'submitted');
     }

     /**
      * Relationship with UserPromotions
      */
     public function userPromotions()
     {
         return $this->hasMany(UserPromotionDetail::class, 'user_id');
     }

     /**
      * Get active promotion for specific assessment type
      */
    public function activePromotion($assessmentType)
    {
        return $this->userPromotions()
            ->active()
            ->ofAssessmentType($assessmentType)
            ->first();
    }
    public function getPromotionLevelAttribute(): string
    {
        // Use already-loaded relation if available (avoids N+1 when eager loaded)
        $promotions = $this->relationLoaded('userPromotions')
            ? $this->userPromotions
            : $this->userPromotions()
                ->where('current_status', true)
                ->with('promotionDetail.currentPromotion')
                ->get();

        $promotions = $promotions
            ->where('current_status', true)
            ->keyBy('assessment_type');

        $labels = ['iq' => 'IQ', 'eq' => 'EQ', 'lq' => 'LQ'];
        $parts  = [];

        foreach ($labels as $key => $label) {
            $name = $promotions->get($key)?->promotionDetail?->currentPromotion?->name ?? '—';
            $parts[] = "{$label}: {$name}";
        }

        return implode('  |  ', $parts);
    }
}
