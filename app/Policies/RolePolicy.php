<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function store(User $user)
    {
        return false;
    }

    public function update(User $user)
    {
        return false;
    }

    public function destroy(User $user)
    {
        return false;
    }
}
