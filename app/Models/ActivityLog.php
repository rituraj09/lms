<?php
// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'user_type', 'action',
        'model_type', 'model_id', 'description',
        'properties', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];
}
