<?php
// app/Models/AssessmentMaster/AssessmentGroup.php

namespace App\Models\AssessmentMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Unguarded;

#[Unguarded]
class AssessmentGroup extends Model
{
    use HasFactory, SoftDeletes;


    protected $casts = [
        'suffle_question'                => 'boolean',
        'allow_back_to_group_question'   => 'boolean',
        'allow_back_to_previous_question'=> 'boolean',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function questionGroup(): BelongsTo
    {
        return $this->belongsTo(\App\Models\QuestionMaster\QuestionGroup::class, 'question_group_id');
    }

    public function assessmentQuestions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class, 'assessment_group_id');
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
