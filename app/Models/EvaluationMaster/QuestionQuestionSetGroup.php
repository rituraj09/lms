<?php

namespace App\Models\EvaluationMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class QuestionQuestionSetGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'question_question_set_groups';

    protected $fillable = [
        'question_set_group_id', 'question_id',
        'order', 'score_override', 'timer', 'negative_mark', 'status',
    ];

    protected $casts = [
        'score_override' => 'decimal:2',
        'negative_mark'  => 'decimal:2',
        'order'          => 'integer',
        'timer'          => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(QuestionSetGroup::class, 'question_set_group_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
