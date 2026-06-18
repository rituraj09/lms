<?php
// app/Models/QuestionMaster/Question.php

namespace App\Models\QuestionMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_group_id',
        'question_code',
        'primary_skill_id',
        'sub_skill_id',
        'difficulty_level_id',
        'age_group_id',
        'answer_category',
        'question_content',
        'explaination',
        'admin_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'question_content' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────

    public function questionGroup(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class, 'question_group_id');
    }

    public function primarySkill(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\PrimarySkillType::class, 'primary_skill_id');
    }

    public function subSkill(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\SubSkillType::class, 'sub_skill_id');
    }

    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\DifficultyLevel::class, 'difficulty_level_id');
    }

    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\AgeGroup::class, 'age_group_id');
    }

    public function assessmentQuestions(): HasMany
    {
        return $this->hasMany(\App\Models\AssessmentMaster\AssessmentQuestion::class, 'question_id');
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
     * Check if this question is used in any assessment.
     * If yes, editing and deletion are restricted.
     */
    public function isUsedInAssessment(): bool
    {
        return $this->assessmentQuestions()->exists();
    }

    // ─── Scopes ───────────────────────────────────────────────────────

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('question_code', 'like', "%{$search}%")
              ->orWhere('admin_notes', 'like', "%{$search}%");
        });
    }
}
