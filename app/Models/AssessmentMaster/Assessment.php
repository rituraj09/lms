<?php
// app/Models/AssessmentMaster/Assessment.php
namespace App\Models\AssessmentMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assessment_code',
        'title',
        'instructions',
        'assessment_type_id',
        'age_group_id',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'admin_note',
        'has_negative_mark',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'has_negative_mark' => 'boolean',
        'total_marks'       => 'decimal:2',
        'passing_marks'     => 'decimal:2',
    ];

    public function assessmentGroups(): HasMany
    {
        return $this->hasMany(AssessmentGroup::class, 'assessment_id');
    }

    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationMaster\AgeGroup::class, 'age_group_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }
}
