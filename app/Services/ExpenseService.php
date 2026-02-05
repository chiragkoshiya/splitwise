<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseParticipant;
use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ExpenseService
{
    public function __construct(
        protected BalanceService $balanceService,
        protected LoggingService $loggingService
    ) {}

    /**
     * Create a new expense
     * 
     * @param array $data ['group_id', 'title', 'total_amount', 'paid_by', 'splits' => [['user_id', 'share_amount']]]
     * @return Expense
     */
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $groupId = $data['group_id'];
            $paidBy = $data['paid_by'];
            $totalAmount = (float)$data['total_amount'];
            $splits = $data['splits'] ?? [];
            $createdBy = Auth::id();

            // 1. Validate group membership
            $group = Group::findOrFail($groupId);
            if (!$group->users()->where('users.id', $paidBy)->exists()) {
                throw new InvalidArgumentException("The payer must be a member of the group.");
            }

            // 2. Validate participants
            foreach ($splits as $split) {
                if (!$group->users()->where('users.id', $split['user_id'])->exists()) {
                    throw new InvalidArgumentException("All split participants must be members of the group.");
                }
            }

            // 3. Validate split totals
            $splitTotal = collect($splits)->sum('share_amount');
            if (abs($splitTotal - $totalAmount) > 0.01) {
                throw new InvalidArgumentException("Split total ($splitTotal) must equal total amount ($totalAmount).");
            }

            // 4. Create expense
            $expense = Expense::create([
                'group_id' => $groupId,
                'title' => $data['title'],
                'total_amount' => $totalAmount,
                'paid_by' => $paidBy,
                'created_by' => $createdBy,
            ]);

            // 5. Create participants & 6. Create splits
            foreach ($splits as $split) {
                // Participant record (anyone in the split)
                ExpenseParticipant::create([
                    'expense_id' => $expense->id,
                    'user_id' => $split['user_id'],
                ]);

                // Split record
                ExpenseSplit::create([
                    'expense_id' => $expense->id,
                    'user_id' => $split['user_id'],
                    'share_amount' => $split['share_amount'],
                ]);
            }

            // 7. Call BalanceService
            $this->balanceService->updateBalancesFromExpense($expense);

            // 8. Create financial logs - Handled internally by BalanceService

            // 9. Create activity logs
            $this->loggingService->logActivity(
                'expenses',
                'created',
                Expense::class,
                $expense->id,
                ['title' => $expense->title, 'amount' => $totalAmount]
            );

            return $expense;
        });
    }

    /**
     * Update an existing expense
     */
    public function updateExpense(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            // Reverse current balances FIRST
            $this->balanceService->reverseBalancesFromExpense($expense);

            // Remove old participants and splits (actually soft deletes if using SoftDeletes)
            $expense->participants()->delete();
            $expense->splits()->delete();

            $groupId = $data['group_id'] ?? $expense->group_id;
            $paidBy = $data['paid_by'] ?? $expense->paid_by;
            $totalAmount = (float)($data['total_amount'] ?? $expense->total_amount);
            $splits = $data['splits'] ?? [];

            // Re-validate split totals
            $splitTotal = collect($splits)->sum('share_amount');
            if (abs($splitTotal - $totalAmount) > 0.01) {
                throw new InvalidArgumentException("Split total ($splitTotal) must equal total amount ($totalAmount).");
            }

            // Update Expense
            $expense->update([
                'title' => $data['title'] ?? $expense->title,
                'total_amount' => $totalAmount,
                'paid_by' => $paidBy,
                'group_id' => $groupId,
            ]);

            // Create new participants and splits
            foreach ($splits as $split) {
                ExpenseParticipant::create([
                    'expense_id' => $expense->id,
                    'user_id' => $split['user_id'],
                ]);

                ExpenseSplit::create([
                    'expense_id' => $expense->id,
                    'user_id' => $split['user_id'],
                    'share_amount' => $split['share_amount'],
                ]);
            }

            // Apply new balances
            $this->balanceService->updateBalancesFromExpense($expense);

            // Log Activity
            $this->loggingService->logActivity(
                'expenses',
                'updated',
                Expense::class,
                $expense->id,
                ['title' => $expense->title, 'amount' => $totalAmount]
            );

            return $expense;
        });
    }

    /**
     * Delete an expense
     */
    public function deleteExpense(Expense $expense): bool
    {
        return DB::transaction(function () use ($expense) {
            // Reverse balances
            $this->balanceService->reverseBalancesFromExpense($expense);

            // Log activity before deletion
            $this->loggingService->logActivity(
                'expenses',
                'deleted',
                Expense::class,
                $expense->id,
                ['title' => $expense->title, 'amount' => $expense->total_amount]
            );

            // Soft delete relations
            $expense->participants()->delete();
            $expense->splits()->delete();

            // Soft delete expense
            return $expense->delete();
        });
    }
}
