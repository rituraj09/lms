<?php
// app/Models/TestAttempt/TestAttemptResponse.php

namespace App\Models\TestAttempt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AssessmentMaster\AssessmentQuestion;

class TestAttemptResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'assessment_question_id',
        'response',
        'obtained_marks',
        'is_correct',
    ];

    protected $casts = [
        'response' => 'array',
        'obtained_marks' => 'decimal:2',
        'is_correct' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function assessmentQuestion(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function isAnswered(): bool
    {
        return !empty($this->response);
    }

    public function isCorrect(): bool
    {
        return $this->is_correct === true;
    }

    public function isIncorrect(): bool
    {
        return $this->is_correct === false;
    }

    public function isUnanswered(): bool
    {
        return empty($this->response);
    }
}
