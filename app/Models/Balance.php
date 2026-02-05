<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;
    
    /**
     * SECURITY: Block ALL mass assignment to prevent direct balance manipulation.
     * Balances can only be modified through BalanceService.
     */
    protected $guarded = ['*'];

    /**
     * Boot method to enforce read-only constraint
     */
    protected static function booted()
    {
        // Prevent updates outside BalanceService
        static::updating(function ($balance) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
            $isFromService = collect($trace)->contains(function ($frame) {
                return isset($frame['class']) && 
                       $frame['class'] === 'App\\Services\\BalanceService';
            });
            
            if (!$isFromService) {
                throw new \Exception(
                    'SECURITY VIOLATION: Direct Balance updates are forbidden. ' .
                    'Use BalanceService::adjustPairBalance() instead.'
                );
            }
        });
        
        // Prevent creation outside BalanceService
        static::creating(function ($balance) {
            if (!app()->runningInConsole() && !defined('BALANCE_SERVICE_CONTEXT')) {
                throw new \Exception(
                    'SECURITY VIOLATION: Direct Balance creation is forbidden. ' .
                    'Use BalanceService::adjustPairBalance() instead.'
                );
            }
        });
        
        // Prevent deletion - balances are immutable
        static::deleting(function () {
            throw new \Exception(
                'SECURITY VIOLATION: Balances cannot be deleted. ' .
                'Create a reversal entry through BalanceService instead.'
            );
        });
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the group that owns the balance.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who owes money (from_user_id).
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who is owed money (to_user_id).
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
