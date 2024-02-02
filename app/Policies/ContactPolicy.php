<?php

namespace App\Policies;

use App\Models\User;

class ContactPolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user)
    {
        return $user->role=='admin';
    }
}
