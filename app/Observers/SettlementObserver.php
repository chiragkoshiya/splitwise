<?php

namespace App\Observers;

use App\Models\Settlement;

class SettlementObserver
{
    /**
     * Handle the Settlement "created" event.
     */
    public function created(Settlement $settlement): void
    {
        //
    }

    /**
     * Handle the Settlement "updated" event.
     */
    public function updated(Settlement $settlement): void
    {
        //
    }

    /**
     * Handle the Settlement "deleted" event.
     */
    public function deleted(Settlement $settlement): void
    {
        //
    }

    /**
     * Handle the Settlement "restored" event.
     */
    public function restored(Settlement $settlement): void
    {
        //
    }

    /**
     * Handle the Settlement "force deleted" event.
     */
    public function forceDeleted(Settlement $settlement): void
    {
        //
    }
}
