<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Base component that enforces authorization before mounting.
 * All financial Livewire components MUST extend this class.
 */
abstract class SecureComponent extends Component
{
    /**
     * Implement authorization logic in child components.
     * This method is called BEFORE secureMount().
     * 
     * Typical implementation:
     * - Verify user has access to the resource
     * - Check group membership
     * - Verify policies
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    abstract protected function authorizeAccess(): void;

    /**
     * Implement your mount logic here instead of mount().
     * This is called AFTER authorization passes.
     */
    abstract protected function secureMount(...$params): void;

    /**
     * Final mount method - DO NOT OVERRIDE in child components.
     * This ensures authorization happens before any data loading.
     */
    final public function mount(...$params)
    {
        // Step 1: Authorize first (before loading any data)
        $this->authorizeAccess();
        
        // Step 2: If authorization passes, proceed with mount logic
        $this->secureMount(...$params);
    }
}
