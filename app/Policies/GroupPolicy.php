<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function update(User $user, Group $group): bool
    {
        if ($user->id === $group->user_id) {
            return true;
        }

        return $group->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->id === $group->user_id;
    }
}
