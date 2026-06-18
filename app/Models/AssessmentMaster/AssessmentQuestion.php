<?php

// app/Models/AssessmentMaster/AssessmentQuestion.php
namespace App\Models\AssessmentMaster;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assessment_group_id',
        'question_id',
        'negative_mark',
        'question_timer',
        'question_type_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'negative_mark' => 'decimal:2',
    ];

    public function assessmentGroup(): BelongsTo
    {
        return $this->belongsTo(AssessmentGroup::class, 'assessment_group_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(\App\ModelsQuestionMaster\Question::class, 'question_id');
    }

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(\App\ModelsQuestionMaster\QuestionType::class, 'question_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }
}
