<?php

namespace App\Helper;
use App\Models\EvaluationMaster\Question;
use Carbon\Carbon;

class Code
{
    public static function generateQuestionCode(int $userId): string
    {
        $year = Carbon::now()->format('y'); // 26

        $count = Question::where('created_by', $userId)
            ->whereYear('created_at', Carbon::now()->year)
            ->count() + 1;

        return sprintf(
            'Q-%04d%s%04d',
            $userId,
            $year,
            $count
        );
    }
}
