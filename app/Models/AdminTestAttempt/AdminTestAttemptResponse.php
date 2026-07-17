<?php
// app/Models/AdminTestAttempt/AdminTestAttemptResponse.php

namespace App\Models\AdminTestAttempt;

use App\Models\AssessmentMaster\AssessmentQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminTestAttemptResponse extends Model
{
    protected $table = 'admin_test_attempt_responses';

    protected $fillable = [
        'admin_test_attempt_id',
        'assessment_question_id',
        'response',
        'obtained_marks',
        'is_correct',
    ];

    protected $casts = [
        'response'       => 'array',
        'obtained_marks' => 'float',
        'is_correct'     => 'boolean',
    ];

    public function adminTestAttempt(): BelongsTo
    {
        return $this->belongsTo(AdminTestAttempt::class, 'admin_test_attempt_id');
    }

    public function assessmentQuestion(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
}
