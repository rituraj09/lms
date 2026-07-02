<?php

namespace App\Models\EvaluationMaster;
use App\Models\AssessmentMaster\Assessment;
use App\Models\QuestionMaster\Question;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class AgeGroup extends Model
{
    use SoftDeletes;
     public $timestamps = false;

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

}
