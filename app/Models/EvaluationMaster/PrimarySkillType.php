<?php

namespace App\Models\EvaluationMaster;
use App\Models\QuestionMaster\Question;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
#[Unguarded]
class PrimarySkillType extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class, 'primary_skill_id');
    }

    // Helper method to identify skill type
    public function isCognitive()
    {
        return $this->slug === 'cognitive';
    }

    public function isLife()
    {
        return $this->slug === 'life';
    }

    public function isLeadership()
    {
        return $this->slug === 'leadership';
    }
}
