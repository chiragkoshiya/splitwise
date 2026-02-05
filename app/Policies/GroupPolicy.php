<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    /**
     * Determine if the user can view any groups.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the group.
     */
    public function view(User $user, Group $group): bool
    {
        return $group->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine if the user can create groups.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the group.
     */
    public function update(User $user, Group $group): bool
    {
        return $group->created_by === $user->id;
    }

    /**
     * Determine if the user can delete the group.
     */
    public function delete(User $user, Group $group): bool
    {
        return $group->created_by === $user->id;
    }

    /**
     * Determine if the user can restore the group.
     */
    public function restore(User $user, Group $group): bool
    {
        return $group->created_by === $user->id;
    }

    /**
     * Determine if the user can permanently delete the group.
     */
    public function forceDelete(User $user, Group $group): bool
    {
        return $group->created_by === $user->id;
    }
}
