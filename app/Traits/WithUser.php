<?php

namespace App\Traits;

use App\Models\User;

trait WithUser
{
    public User $user;
    public function bootWithUser(): void
    {
        $this->user = User::findOrFail(auth('user')->id());
    }
}
