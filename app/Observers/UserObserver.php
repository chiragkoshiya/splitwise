<?php

namespace App\Observers;

use App\Models\User;
use App\Services\LoggingService;

class UserObserver
{
    public function __construct(
        protected LoggingService $loggingService
    ) {}

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->loggingService->logAuthEvent(
            userId: $user->id,
            action: 'registered',
            success: true,
            metadata: ['email' => $user->email]
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('password')) {
            $this->loggingService->logAuthEvent(
                userId: $user->id,
                action: 'password_changed',
                success: true
            );
        }
    }
}
