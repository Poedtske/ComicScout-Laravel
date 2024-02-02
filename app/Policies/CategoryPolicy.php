<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user)
     {
        return $user->role=='admin';
     }
     public function store(User $user)
    {
        return $user->role=='admin';
    }

    public function update(User $user,Category $category)
    {
        // return $user->id === $post->user_id;
        return $user->role=='admin';
    }

    public function delete(User $user,Category $category)
    {
        // return $user->id === $post->user_id;
        return $user->role=='admin';
    }
}
