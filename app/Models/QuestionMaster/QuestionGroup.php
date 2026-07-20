<?php
// app/Models/QuestionMaster/QuestionGroup.php

namespace App\Models\QuestionMaster;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


#[Unguarded]
class QuestionGroup extends Model
{
    use  SoftDeletes;


    protected $casts = [
        'group_content' => 'array',   // ✅ correct column
    ];

    // ─── Relationships ────────────────────────────────────────────────

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_group_id');
    }

    public function assessmentGroups(): HasMany
    {
        return $this->hasMany(\App\Models\AssessmentMaster\AssessmentGroup::class, 'question_group_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }

    // ─── Business Logic Helpers ───────────────────────────────────────

    /**
     * Check if this group is linked to any assessment group.
     * If yes, content editing and deletion are restricted.
     */
    public function isLinkedToAssessment(): bool
    {
        return $this->assessmentGroups()->exists();
    }

    /**
     * Check if a specific question in this group is used
     * inside any assessment_questions record.
     */
    public function hasQuestionsInAssessment(): bool
    {
        return $this->questions()
            ->whereHas('assessmentQuestions')
            ->exists();
    }

    // ─── Scopes ───────────────────────────────────────────────────────

    public function scopeSearch($query, string $term)
    {
        $term = mb_strtolower($term); // ✅ lowercase the search term

        return $query->where(function ($q) use ($term) {
            $q->whereRaw(
                    "LOWER(JSON_UNQUOTE(JSON_EXTRACT(group_content, '$.title.en'))) LIKE ?",
                    ["%{$term}%"]
                )
                ->orWhereRaw(
                    "LOWER(JSON_UNQUOTE(JSON_EXTRACT(group_content, '$.content.en'))) LIKE ?",
                    ["%{$term}%"]
                )
                ->orWhereRaw(
                    "LOWER(group_code) LIKE ?",
                    ["%{$term}%"]
                );
        });
    }

    /**
     * Handy accessor  →  $group->group_title
     */
    public function getGroupTitleAttribute(): string
    {
        return data_get($this->group_content, 'title.en')
            ?? $this->group_code
            ?? '—';
    }
    public function scopeByCategory($query, string $category)
    {
        return $query->where('questions_category', $category);
    }
}
