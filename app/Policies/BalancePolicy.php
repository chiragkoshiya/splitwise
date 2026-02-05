<?php

namespace App\Policies;

use App\Models\Balance;
use App\Models\User;

class BalancePolicy
{
    /**
     * Determine if the user can view any balances.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the balance.
     */
    public function view(User $user, Balance $balance): bool
    {
        return $balance->group->users()->where('user_id', $user->id)->exists();
    }
}
