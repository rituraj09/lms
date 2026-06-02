<?php

namespace App\Models\EvaluationMaster;
use App\Models\EvaluationMaster\QuestionType;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[Unguarded]
class Question extends Model
{
    use SoftDeletes;
    protected $casts = [
        'question_contents' => 'array',
    ];
    // Automatically set created_by and updated_by
    protected static function booted()
    {
        static::creating(function ($model) {
            // Use authenticated user ID or default to 1
            $model->created_by = auth()->id() ?? 1;
            $model->updated_by = auth()->id() ?? 1;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id() ?? 1;
        });
    }
    public $timestamps = false;
    public function question_type(){
        return $this->belongsTo(QuestionType::class,'question_type_id')->withDefault();
    }
}
