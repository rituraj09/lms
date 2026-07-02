<?php

namespace App\Models\EvaluationMaster;
use App\Models\QuestionMaster\Question;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Unguarded]
class DifficultyLevel extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
