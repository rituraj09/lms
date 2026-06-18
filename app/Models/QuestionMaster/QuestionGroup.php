<?php
// app/Models/QuestionMaster/QuestionGroup.php

namespace App\Models\QuestionMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_code',
        'questions_category',
        'title',
        'group_content',
        'admin_note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'group_content' => 'array',
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

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('group_code', 'like', "%{$search}%")
              ->orWhere('admin_note', 'like', "%{$search}%");
        });
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('questions_category', $category);
    }
}
