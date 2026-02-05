<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\LoggingService;

class ExpenseObserver
{
    public function __construct(
        protected LoggingService $loggingService
    ) {}

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->loggingService->logExpenseCreated($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $oldData = $expense->getOriginal();
        $this->loggingService->logExpenseUpdated($expense, $oldData);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $this->loggingService->logActivity(
            module: 'expenses',
            action: 'deleted',
            entityType: Expense::class,
            entityId: $expense->id,
            metadata: [
                'title' => $expense->title,
                'amount' => $expense->total_amount
            ]
        );
    }
}
