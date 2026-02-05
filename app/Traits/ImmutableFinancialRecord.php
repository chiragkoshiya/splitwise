<?php

namespace App\Traits;

trait ImmutableFinancialRecord
{
    /**
     * Boot the immutable trait.
     */
    protected static function bootImmutableFinancialRecord()
    {
        // Prevent updates
        static::updating(function ($model) {
            if (app()->runningInConsole() || defined('ALLOW_IMMUTABLE_UPDATE')) {
                return; // Backdoor for seeds/tests/special migrations if flag is set
            }
            
            throw new \Exception(
                sprintf(
                    'SECURITY VIOLATION: %s records are immutable. Create a reversal entry instead.',
                    class_basename($model)
                )
            );
        });
        
        // Prevent force deletion
        static::deleting(function ($model) {
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model)) && !$model->isForceDeleting()) {
                // Allow soft delete if model supports it
                return;
            }
            
            if (app()->runningInConsole() || defined('ALLOW_IMMUTABLE_DELETE')) {
                return;
            }
            
            throw new \Exception(
                sprintf(
                    'SECURITY VIOLATION: %s records cannot be deleted from the ledger.',
                    class_basename($model)
                )
            );
        });
    }
}
