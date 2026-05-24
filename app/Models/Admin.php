<?php

namespace App\Models;

use App\Extends\Role;
use App\Models\Master\AdminDetails;
use App\Models\Master\Organisation;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Unguarded]
class Admin extends Authenticatable
{
    use HasRoles, SoftDeletes;

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function organisations()
    {
        return $this->belongsToMany(Organisation::class,'admin_organisations','admin_id','organisation_id')->withTrashed();
    }
    public function currentrole()
    {
        return $this->belongsTo(Role::class,'current_role_id')->withDefault();
    }
    public function currentorganisation()
    {
        return $this->belongsTo(Organisation::class,'current_organisation_id')->withDefault()->withTrashed();
    }
    public function details() {
        return $this->hasOne(AdminDetails::class , 'admin_id')->withDefault();
    }

}
