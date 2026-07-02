<?php

namespace App\Models\TestAttempt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PromotionMaster\PromotionDetail;
use App\Models\User;

class UserPromotionDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'promotion_details_id',
        'assessment_type',
        'test_attempt_id',
        'current_status',
    ];

    protected $casts = [
        'user_id'              => 'integer',
        'promotion_details_id' => 'integer',
        'test_attempt_id'      => 'integer',
        'current_status'       => 'boolean',
    ];

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the promotion detail.
     */
    public function promotionDetail(): BelongsTo
    {
        return $this->belongsTo(PromotionDetail::class, 'promotion_details_id');
    }

    /**
     * Get the test attempt.
     */
    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    /**
     * Scope to get current active promotions.
     */
    public function scopeCurrent($query)
    {
        return $query->where('current_status', true);
    }

    /**
     * Scope to filter by assessment type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('assessment_type', $type);
    }
}
