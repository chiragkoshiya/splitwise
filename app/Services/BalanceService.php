<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BalanceService
{
    public function __construct(
        protected LoggingService $loggingService
    ) {}

    /**
     * Update balances based on expense creation
     */
    public function updateBalancesFromExpense(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $payerId = $expense->paid_by;
            $groupId = $expense->group_id;

            // Loop through splits (people who owe money)
            // splits table contains 'user_id' and 'share_amount'
            foreach ($expense->splits as $split) {
                if ($split->user_id === $payerId) continue; // Skip payer's own share

                // Logic: Debtor (split->user_id) pays Creditor (payerId)
                $this->adjustPairBalance(
                    $groupId, 
                    $split->user_id, 
                    $payerId, 
                    (float)$split->share_amount, 
                    'expense', 
                    Expense::class, 
                    $expense->id
                );
            }
        });
    }

    /**
     * Reverse balances (for deleting/editing an expense)
     */
    public function reverseBalancesFromExpense(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $payerId = $expense->paid_by;
            $groupId = $expense->group_id;

            foreach ($expense->splits as $split) {
                if ($split->user_id === $payerId) continue;
                
                // Subtract the amount originally added
                $this->adjustPairBalance(
                    $groupId, 
                    $split->user_id, 
                    $payerId, 
                    -(float)$split->share_amount, 
                    'expense_reversal', 
                    Expense::class, 
                    $expense->id
                );
            }
        });
    }

    /**
     * Process a settlement payment
     */
    public function updateBalancesFromSettlement(Settlement $settlement): void
    {
        DB::transaction(function () use ($settlement) {
            // Logic: paid_from owes paid_to. Settlement reduces that debt.
            $this->adjustPairBalance(
                $settlement->group_id,
                $settlement->paid_from,
                $settlement->paid_to,
                -(float)$settlement->amount, // Reduced debt
                'settlement',
                Settlement::class,
                $settlement->id
            );
        });
    }

    /**
     * Reverse a settlement payment
     */
    public function reverseBalancesFromSettlement(Settlement $settlement): void
    {
        DB::transaction(function () use ($settlement) {
            // Logic: Reverse the reduction by adding the amount back.
            $this->adjustPairBalance(
                $settlement->group_id,
                $settlement->paid_from,
                $settlement->paid_to,
                (float)$settlement->amount, // Add debt back
                'settlement_reversal',
                Settlement::class,
                $settlement->id
            );
        });
    }

    /**
     * Adjust balance between two users (U1 owes U2)
     * This is the core engine method using pair normalization.
     */
    protected function adjustPairBalance(int $groupId, int $debtorId, int $creditorId, float $delta, string $type, string $relatedType, int $relatedId): void
    {
        if ($debtorId === $creditorId) return;

        // Normalization: Key by lower ID first to ensure a single row per pair
        $u1 = min($debtorId, $creditorId);
        $u2 = max($debtorId, $creditorId);
        
        // If the normalized debtor is the actual debtor, delta is positive (U1 owes U2)
        // If the normalized debtor is the creditor, delta is negative (U2 owes U1)
        $actualDelta = ($u1 === $debtorId) ? $delta : -$delta;

        $balance = Balance::where([
            ['group_id', '=', $groupId],
            ['from_user_id', '=', $u1],
            ['to_user_id', '=', $u2],
        ])->lockForUpdate()->first();

        $oldAmount = $balance ? (float)$balance->amount : 0.0;
        $newAmount = $oldAmount + $actualDelta;

        if ($balance) {
            $balance->update(['amount' => $newAmount]);
        } else {
            $balance = Balance::create([
                'group_id' => $groupId,
                'from_user_id' => $u1,
                'to_user_id' => $u2,
                'amount' => $newAmount,
            ]);
        }

        // Financial Audit Log
        $this->loggingService->logFinancialTransaction(
            $groupId, 
            $debtorId, 
            $creditorId, 
            abs($delta),
            $type, 
            $relatedType, 
            $relatedId,
            $oldAmount, 
            $newAmount, 
            "Adjustment of delta $delta between user $debtorId and $creditorId"
        );
    }

    /**
     * Get net balance between two users in a group
     */
    public function getNetBalanceBetweenUsers(int $groupId, int $userId1, int $userId2): float
    {
        $u1 = min($userId1, $userId2);
        $u2 = max($userId1, $userId2);

        $balance = Balance::where([
            ['group_id', '=', $groupId],
            ['from_user_id', '=', $u1],
            ['to_user_id', '=', $u2],
        ])->first();

        if (!$balance) return 0.0;

        // If requested user is U1, return amount (how much U1 owes U2)
        // If requested user is U2, return -amount (how much U2 owes U1)
        return ($userId1 === $u1) ? (float)$balance->amount : -(float)$balance->amount;
    }

    /**
     * Get all balances for a group
     */
    public function getGroupBalances(int $groupId)
    {
        return Balance::where('group_id', $groupId)
            ->with(['fromUser', 'toUser'])
            ->get();
    }

    /**
     * Get all balances for a user in a group
     */
    public function getUserBalancesForGroup(int $groupId, int $userId)
    {
        return Balance::where('group_id', $groupId)
            ->where(function ($query) use ($userId) {
                $query->where('from_user_id', $userId)
                      ->orWhere('to_user_id', $userId);
            })
            ->with(['fromUser', 'toUser'])
            ->get();
    }

    /**
     * Get aggregate balance summary for a user across all groups
     */
    public function getUserBalanceSummary(int $userId): array
    {
        $balances = Balance::where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->get();

        $youOwe = 0;
        $youAreOwed = 0;

        foreach ($balances as $balance) {
            $amount = (float)$balance->amount;
            
            if ($balance->from_user_id === $userId) {
                // User is U1. If positive, U1 owes U2. If negative, U2 owes U1.
                if ($amount > 0) {
                    $youOwe += $amount;
                } else {
                    $youAreOwed += abs($amount);
                }
            } else {
                // User is U2. If positive, U1 owes U2. If negative, U2 owes U1.
                if ($amount > 0) {
                    $youAreOwed += $amount;
                } else {
                    $youOwe += abs($amount);
                }
            }
        }

        return [
            'total' => $youAreOwed - $youOwe,
            'owe' => $youOwe,
            'owed' => $youAreOwed,
        ];
    }
}
