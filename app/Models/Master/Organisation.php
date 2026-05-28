<?php

namespace App\Models\Master;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Unguarded]
class Organisation extends Model
{
    use SoftDeletes;
    public $casts = [
        'social_links' => 'array',
    ];
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'organisation_type',
        'state_id',
        'pincode',
        'website',
        'social_links',
        'logo_path',
    ];

    public function state(){
        return $this->belongsTo(State::class,'state_id')->withDefault();
    }
    public function district(){
        return $this->belongsTo(District::class,'district_id')->withDefault();
    }

    public function admins(){
        return $this->belongsToMany(Admin::class,'admin_organisations','organisation_id','admin_id')->withTrashed();
    }

    public function users(){
        return $this->belongsToMany(User::class,'user_organisations','organisation_id','user_id')->withTrashed();
    }
}
