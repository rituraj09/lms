<?php
// app/Models/TestAttempt/TestAttempt.php

namespace App\Models\TestAttempt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AssessmentMaster\Assessment;
use App\Models\User;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'ack_no',
        'assessment_id',
        'user_id',
        'started_at',
        'submitted_at',
        'negative_score',
        'total_score',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'negative_score' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TestAttemptResponse::class, 'test_attempt_id');
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeEvaluated($query)
    {
        return $query->where('status', 'evaluated');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isEvaluated(): bool
    {
        return $this->status === 'evaluated';
    }

    public function canBeResumed(): bool
    {
        return $this->isInProgress() && $this->started_at !== null;
    }

    public function getTotalQuestionsCount(): int
    {
        return $this->assessment
            ->assessmentGroups()
            ->withCount('assessmentQuestions')
            ->get()
            ->sum('assessment_questions_count');
    }

    public function getAnsweredQuestionsCount(): int
    {
        return $this->responses()->whereNotNull('response')->count();
    }

    public function getDurationInMinutes(): ?int
    {
        if (!$this->started_at || !$this->submitted_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->submitted_at);
    }

    public function getTimeElapsed(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        return $this->started_at->diffInSeconds(now());
    }

    public function getTimeRemaining(): ?int
    {
        if (!$this->started_at || !$this->assessment->duration_minutes) {
            return null;
        }

        $totalSeconds = $this->assessment->duration_minutes * 60;
        $elapsed = $this->getTimeElapsed();

        return max(0, $totalSeconds - $elapsed);
    }

    public function hasExpired(): bool
    {
        if (!$this->assessment->duration_minutes || !$this->started_at) {
            return false;
        }

        return $this->getTimeRemaining() <= 0;
    }

    // ─── Generate Acknowledgment Number ───────────────────────

    public static function generateAckNo(): string
    {
        do {
            $ackNo = 'ACK-' . strtoupper(\Illuminate\Support\Str::random(10));
        } while (self::where('ack_no', $ackNo)->exists());

        return $ackNo;
    }

    public function isPassed()
    {
        return $this->total_score >= $this->assessment->passing_marks;
    }
}
