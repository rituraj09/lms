<?php

namespace App\Models\PromotionMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EvaluationMaster\AgeGroup;
use App\Models\EvaluationMaster\DifficultyLevel;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'age_group_id',
        'difficulty_level_id',
        'badge',
    ];

    protected $casts = [
        'age_group_id'       => 'integer',
        'difficulty_level_id' => 'integer',
    ];

    /**
     * Get the age group of the promotion.
     */
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class, 'age_group_id');
    }

    /**
     * Get the difficulty level of the promotion.
     */
    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(DifficultyLevel::class, 'difficulty_level_id');
    }

    /**
     * Get the promotion details where this promotion is current.
     */
    public function currentPromotionDetails(): HasMany
    {
        return $this->hasMany(PromotionDetail::class, 'current_promotion_id');
    }

    /**
     * Get the promotion details where this promotion is next.
     */
    public function nextPromotionDetails(): HasMany
    {
        return $this->hasMany(PromotionDetail::class, 'next_promotion_id');
    }
}
