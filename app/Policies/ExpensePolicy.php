<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine if the user can view any expenses.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $expense->group->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine if the user can create expenses.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $expense->created_by === $user->id;
    }

    /**
     * Determine if the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $expense->created_by === $user->id || $expense->group->created_by === $user->id;
    }

    /**
     * Determine if the user can restore the expense.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $expense->created_by === $user->id || $expense->group->created_by === $user->id;
    }

    /**
     * Determine if the user can permanently delete the expense.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $expense->created_by === $user->id || $expense->group->created_by === $user->id;
    }
}
