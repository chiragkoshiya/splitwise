<?php

namespace App\Policies;

use App\Models\Settlement;
use App\Models\User;

class SettlementPolicy
{
    /**
     * Determine if the user can view any settlements.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the settlement.
     */
    public function view(User $user, Settlement $settlement): bool
    {
        return $settlement->paid_from === $user->id || $settlement->paid_to === $user->id;
    }

    /**
     * Determine if the user can create settlements.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the settlement.
     */
    public function update(User $user, Settlement $settlement): bool
    {
        return $settlement->created_by === $user->id || $settlement->group->created_by === $user->id;
    }

    /**
     * Determine if the user can delete the settlement.
     */
    public function delete(User $user, Settlement $settlement): bool
    {
        return $settlement->created_by === $user->id || $settlement->group->created_by === $user->id;
    }

    /**
     * Determine if the user can restore the settlement.
     */
    public function restore(User $user, Settlement $settlement): bool
    {
        return $settlement->created_by === $user->id || $settlement->group->created_by === $user->id;
    }

    /**
     * Determine if the user can permanently delete the settlement.
     */
    public function forceDelete(User $user, Settlement $settlement): bool
    {
        return $settlement->created_by === $user->id || $settlement->group->created_by === $user->id;
    }
}
