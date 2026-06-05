<?php

namespace App\Models\EvaluationMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class QuestionSet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'question_sets';

    protected $fillable = [
        'code', 'title', 'slug', 'question_set_type', 'description',
        'age_group_id', 'image_path', 'timer', 'total_questions',
        'passing_score', 'randomize_questions', 'status',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'timer'               => 'integer',
        'total_questions'     => 'integer',
        'passing_score'       => 'integer',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(QuestionSetGroup::class, 'question_set_id')->orderBy('id');
    }

    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }

    /** Re-count active questions across all groups and persist. */
    public function recalculateTotalQuestions(): void
    {
        $this->update([
            'total_questions' => $this->groups()
                ->withCount(['questions as questions_count' => fn ($q) => $q->where('question_question_set_groups.status', 'active')])
                ->get()
                ->sum('questions_count'),
        ]);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }
}
