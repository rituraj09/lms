<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    protected $fillable = [
        'full_name',
        'institution',
        'role',
        'email',
        'phone',
        'preferred_slot',
        'message',
        'status',
    ];
}
