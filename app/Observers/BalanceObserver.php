<?php

namespace App\Observers;

use App\Models\Balance;
use App\Services\LoggingService;

class BalanceObserver
{
    public function __construct(
        protected LoggingService $loggingService
    ) {}

    /**
     * Handle the Balance "updated" event.
     */
    public function updated(Balance $balance): void
    {
        // Log the ground truth shift
        // This acts as a secondary audit trail to ensure every cent moved in the 'balances' table is tracked
        if ($balance->isDirty('amount')) {
            $oldAmount = (float)$balance->getOriginal('amount');
            $newAmount = (float)$balance->amount;
            $diff = $newAmount - $oldAmount;

            $this->loggingService->logActivity(
                module: 'ledger',
                action: 'balance_shift',
                entityType: Balance::class,
                entityId: $balance->id,
                metadata: [
                    'group_id' => $balance->group_id,
                    'from_user_id' => $balance->from_user_id,
                    'to_user_id' => $balance->to_user_id,
                    'before' => $oldAmount,
                    'after' => $newAmount,
                    'delta' => $diff
                ]
            );
        }
    }
}
