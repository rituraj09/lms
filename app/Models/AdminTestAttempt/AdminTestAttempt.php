<?php
// app/Models/AdminTestAttempt/AdminTestAttempt.php

namespace App\Models\AdminTestAttempt;

use App\Models\AssessmentMaster\Assessment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminTestAttempt extends Model
{
    protected $table = 'admin_test_attempts';

    protected $fillable = [
        'ack_no',
        'assessment_id',
        'admin_id',
        'started_at',
        'submitted_at',
        'total_score',
        'negative_score',
        'status',
    ];

    protected $casts = [
        'started_at'    => 'datetime',
        'submitted_at'  => 'datetime',
        'total_score'   => 'float',
        'negative_score'=> 'float',
    ];

    // ── Relationships ──────────────────────────────────────
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AdminTestAttemptResponse::class, 'admin_test_attempt_id');
    }

    // ── Helpers ────────────────────────────────────────────
    public static function generateAckNo(): string
    {
        return 'ADM-' . strtoupper(uniqid());
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function hasExpired(): bool
    {
        if (!$this->started_at) return false;

        $assessment = $this->assessment;
        if (!$assessment || !$assessment->duration_minutes) return false;

        $expiresAt = $this->started_at->addMinutes($assessment->duration_minutes);
        return now()->greaterThan($expiresAt);
    }

    public function getTimeElapsed(): ?int
    {
        if (!$this->started_at) return null;
        return (int) $this->started_at->diffInSeconds(now());
    }

    public function getTimeRemaining(): ?int
    {
        if (!$this->started_at) return null;

        $assessment = $this->assessment;
        if (!$assessment || !$assessment->duration_minutes) return null;

        $totalSeconds   = $assessment->duration_minutes * 60;
        $elapsed        = $this->getTimeElapsed();
        $remaining      = $totalSeconds - $elapsed;

        return max(0, $remaining);
    }

    public function getDurationInMinutes(): ?int
    {
        return $this->assessment?->duration_minutes;
    }

    public function getTotalQuestionsCount(): int
    {
        return $this->assessment?->assessmentGroups
            ->sum(fn($g) => $g->assessmentQuestions->count()) ?? 0;
    }

    public function getAnsweredQuestionsCount(): int
    {
        return $this->responses()->whereNotNull('response')->count();
    }
}
