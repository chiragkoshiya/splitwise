<?php

namespace App\Observers;

use App\Models\Balance;

class BalanceObserver
{
    /**
     * Handle the Balance "created" event.
     */
    public function created(Balance $balance): void
    {
        //
    }

    /**
     * Handle the Balance "updated" event.
     */
    public function updated(Balance $balance): void
    {
        //
    }

    /**
     * Handle the Balance "deleted" event.
     */
    public function deleted(Balance $balance): void
    {
        //
    }
}
