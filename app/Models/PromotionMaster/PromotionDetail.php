<?php

namespace App\Models\PromotionMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TestAttempt\UserPromotionDetail;

class PromotionDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'current_promotion_id',
        'percentage_min',
        'percentage_max',
        'next_promotion_id',
    ];

    protected $casts = [
        'current_promotion_id' => 'integer',
        'next_promotion_id'    => 'integer',
        'percentage_min'       => 'decimal:2',
        'percentage_max'       => 'decimal:2',
    ];

    /**
     * Get the current promotion.
     */
    public function currentPromotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'current_promotion_id');

    }

    /**
     * Get the next promotion.
     */
    public function nextPromotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'next_promotion_id');
    }

    /**
     * Get the user promotion details.
     */
    public function userPromotionDetails(): HasMany
    {
        return $this->hasMany(UserPromotionDetail::class, 'promotion_details_id');
    }
}
