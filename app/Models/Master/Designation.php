<?php

namespace App\Models\Master;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class Designation extends Model
{
     use SoftDeletes;

     public $timestamps = false;
}
