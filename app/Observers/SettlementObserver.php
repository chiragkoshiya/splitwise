<?php

namespace App\Observers;

use App\Models\Settlement;
use App\Services\LoggingService;

class SettlementObserver
{
    public function __construct(
        protected LoggingService $loggingService
    ) {}

    /**
     * Handle the Settlement "created" event.
     */
    public function created(Settlement $settlement): void
    {
        $this->loggingService->logSettlement($settlement);
    }

    /**
     * Handle the Settlement "deleted" event.
     */
    public function deleted(Settlement $settlement): void
    {
        $this->loggingService->logActivity(
            module: 'settlements',
            action: 'deleted',
            entityType: Settlement::class,
            entityId: $settlement->id,
            metadata: [
                'amount' => $settlement->amount,
                'from' => $settlement->paid_from,
                'to' => $settlement->paid_to
            ]
        );
    }
}
