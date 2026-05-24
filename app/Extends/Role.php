<?php

namespace App\Extends;

use App\Models\Master\Organisation;

class Role extends \Spatie\Permission\Models\Role
{
    public function organisation()
    {
        return $this->belongsTo(Organisation::class,'organisation_id')->withDefault()->withTrashed();
    }
}
