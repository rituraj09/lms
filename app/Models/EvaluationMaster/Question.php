<?php

namespace App\Models\EvaluationMaster;

use Illuminate\Database\Eloquent\Model;
#[Unguarded]
class Question extends Model
{
    public function question_type(){
        return $this->belongsTo(QuestionType::class,'question_type_id')->withDefault();
    }
}
