<?php
// app/Traits/WithAdmin.php


namespace App\Traits;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

trait WithAdmin
{
    public Admin $admin;

    public function mountWithAdmin(): void
    {
        $this->admin = Auth::guard('admin')->user();
    }

    public function getAdminProperty(): Admin
    {
        return Auth::guard('admin')->user();
    }
}
