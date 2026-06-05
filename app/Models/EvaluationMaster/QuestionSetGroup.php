<?php

namespace App\Models\EvaluationMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class QuestionSetGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'question_set_groups';

    protected $fillable = [
        'question_set_id', 'title', 'description', 'instructions',
        'question_category', 'randomize_questions', 'allow_main_backtrack',
        'allow_backtrack', 'main_timer', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'description'           => 'array',
        'randomize_questions'   => 'boolean',
        'allow_main_backtrack'  => 'boolean',
        'allow_backtrack'       => 'boolean',
        'main_timer'            => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function questionSet(): BelongsTo
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(
            Question::class,
            'question_question_set_groups',
            'question_set_group_id',
            'question_id'
        )
        ->withPivot(['order', 'score_override', 'timer', 'negative_mark'])
        ->orderByPivot('order')
        ->withTimestamps();
    }

    public function activeQuestions(): BelongsToMany
    {
        return $this->questions()->wherePivot('status', 'active');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }
}
