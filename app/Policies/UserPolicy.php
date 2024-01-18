<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function login(User $user)
    {
        return !$user->is_bloqued;
    }

    public function update(User $user)
    {
        return !$user->is_bloqued;
    }

    public function profil(User $user)
    {
        return !$user->is_bloqued;
    }

    public function refresh(User $user)
    {
        return !$user->is_bloqued;
    }
}
