<?php

namespace App\Traits;

use App\Models\Admin;

trait WithAdmin
{
    public Admin $admin;
    public function bootWithAdmin(){
        $this->admin = Admin::findOrFail(auth('admin')->id())->load('roles');
    }
}
