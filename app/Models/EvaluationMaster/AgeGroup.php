<?php

namespace App\Models\EvaluationMaster;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class AgeGroup extends Model
{
    use SoftDeletes;
     public $timestamps = false;

}
