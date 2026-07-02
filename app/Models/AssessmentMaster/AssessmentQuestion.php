<?php

// app/Models/AssessmentMaster/AssessmentQuestion.php
namespace App\Models\AssessmentMaster;


use App\Models\TestAttempt\TestAttemptResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Unguarded;

#[Unguarded]
class AssessmentQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'negative_mark' => 'decimal:2',
    ];

    public function assessmentGroup(): BelongsTo
    {
        return $this->belongsTo(AssessmentGroup::class, 'assessment_group_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(\App\Models\QuestionMaster\Question::class, 'question_id');
    }

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\QuestionMaster\QuestionType::class, 'question_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }
    public function testAttemptResponses()
    {
        return $this->hasMany(TestAttemptResponse::class);
    }
}
