<?php

namespace App\Traits;

use App\Models\Admin;

trait WithAdmin
{
    public Admin $admin;
    public function bootWithAdmin(): void
    {
        $this->admin = Admin::findOrFail(auth('admin')->id())->load('roles');
    }
}
