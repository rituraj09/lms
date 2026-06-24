<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Models\Master\UserDetail;
use App\Models\Master\Organisation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
}
