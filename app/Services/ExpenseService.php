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
        // SECURITY: Use FormRequest for strict validation (including exact split totals)
        $request = new \App\Http\Requests\CreateExpenseRequest();
        
        // Manually run the validation logic of the FormRequest against the provided data
        $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());
        
        // Apply complex validation hooks from the FormRequest
        $request->withValidator($validator);
        
        // This will throw ValidationException if fails
        $validated = $validator->validate();

        return DB::transaction(function () use ($validated) {
            $groupId = $validated['group_id'];
            $paidBy = $validated['paid_by'];
            $totalAmount = (float)$validated['total_amount'];
            $splits = $validated['splits'] ?? [];
            $createdBy = Auth::id();

            // Note: Group membership and split totals are already validated strictly by CreateExpenseRequest
            // so we can proceed directly to logic.

            // 4. Create expense
            $expense = Expense::create([
                'group_id' => $groupId,
                'title' => $validated['title'],
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
