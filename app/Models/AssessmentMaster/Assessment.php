<?php
// app/Models/AssessmentMaster/Assessment.php
namespace App\Models\AssessmentMaster;

use App\Models\Master\Organisation;
use App\Models\TestAttempt\TestAttempt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Unguarded]
class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'has_negative_mark'       => 'boolean',
        'shuffle_sections'        => 'boolean',
        'show_result_immediately' => 'boolean',
        'show_correct_answers'    => 'boolean',
        'show_explainations'      => 'boolean',
        'total_marks'             => 'decimal:2',
        'passing_marks'           => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function assessmentGroups(): HasMany
    {
        return $this->hasMany(AssessmentGroup::class, 'assessment_id');
    }
    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\DifficultyLevel::class, 'difficulty_level_id');
    }
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\AgeGroup::class, 'age_group_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, 'assessment_organisation')
            ->withPivot([
                'assigned_by',
                'status',
                'assigned_date',
                'expiry_date',
                'assignment_note',
            ])
            ->withTimestamps();
    }

    public function activeOrganisations(): BelongsToMany
    {
        return $this->organisations()
            ->wherePivot('status', 'active')
            ->where('organisations.status', 'active');
    }

    // ── NEW: Test Attempts Relationship ───────────────────────────
    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class, 'assessment_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePublic($query)
    {
        return $query->where('status', 'public');
    }

    public function scopeWithQuestionCount($query)
    {
        return $query->withCount([
            'assessmentGroups as total_questions' => function ($q) {
                $q->join(
                    'assessment_questions',
                    'assessment_groups.id',
                    '=',
                    'assessment_questions.assessment_group_id'
                );
            }
        ]);
    }
    public function assessmentQuestions(): HasManyThrough
    {
        return $this->hasManyThrough(
            AssessmentQuestion::class,
            AssessmentGroup::class,
            'assessment_id',       // Foreign key on assessment_groups
            'assessment_group_id', // Foreign key on assessment_questions
            'id',                  // Local key on assessments
            'id'                   // Local key on assessment_groups
        );
    }
    // ─── Accessors ────────────────────────────────────────────────

    public function getOrganisationsCountAttribute(): int
    {
        return $this->organisations()->count();
    }

    public function getActiveOrganisationsCountAttribute(): int
    {
        return $this->activeOrganisations()->count();
    }

    // ── NEW: Check if assessment has any active attempts ──────────

    /**
     * Check if assessment has any attempts (in_progress, submitted, evaluated)
     */
    public function hasAttempts(): bool
    {
        return $this->testAttempts()->exists();
    }

    /**
     * Check if assessment has any active in_progress attempts
     */
    public function hasActiveAttempts(): bool
    {
        return $this->testAttempts()
            ->where('status', 'in_progress')
            ->exists();
    }

    /**
     * Check if assessment has any completed attempts (submitted or evaluated)
     */
    public function hasCompletedAttempts(): bool
    {
        return $this->testAttempts()
            ->whereIn('status', ['submitted', 'evaluated'])
            ->exists();
    }

    /**
     * Check if assessment is locked for editing
     * Locked = has ANY attempt (in_progress, submitted, evaluated)
     */
    public function isLockedForEditing(): bool
    {
        return $this->hasAttempts();
    }

    /**
     * Get attempt counts summary
     */
    public function getAttemptsSummary(): array
    {
        $attempts = $this->testAttempts()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total'       => array_sum($attempts),
            'in_progress' => $attempts['in_progress'] ?? 0,
            'submitted'   => $attempts['submitted']   ?? 0,
            'evaluated'   => $attempts['evaluated']   ?? 0,
        ];
    }
    // Helper methods to check assessment type
    public function hasIQ()
    {
        return in_array($this->assessment_type_id, ['iq', 'iq+eq', 'iq+lq', 'iq+eq+lq']);
    }

    public function hasEQ()
    {
        return in_array($this->assessment_type_id, ['eq', 'iq+eq', 'eq+lq', 'iq+eq+lq']);
    }

    public function hasLQ()
    {
        return in_array($this->assessment_type_id, ['lq', 'iq+lq', 'eq+lq', 'iq+eq+lq']);
    }
}
