<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function  scopeByState($query,$state_id)
    {
        return $query->where('state_id', $state_id);
    }
}
