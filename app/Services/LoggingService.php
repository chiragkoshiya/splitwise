<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\FinancialLog;
use App\Models\AuthLog;
use App\Models\Expense;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Centralized Logging Service
 * 
 * Handles all activity, financial, and authentication logs for the system.
 * Designed to be immutable (append-only) to maintain a reliable audit trail.
 */
class LoggingService
{
    /**
     * Log when a new expense is created
     */
    public function logExpenseCreated(Expense $expense): void
    {
        $this->logActivity(
            module: 'expenses',
            action: 'created',
            entityType: Expense::class,
            entityId: $expense->id,
            metadata: [
                'title' => $expense->title,
                'amount' => $expense->total_amount,
                'group_id' => $expense->group_id,
                'paid_by' => $expense->paid_by
            ]
        );
    }

    /**
     * Log when an expense is updated
     */
    public function logExpenseUpdated(Expense $expense, array $oldData): void
    {
        $this->logActivity(
            module: 'expenses',
            action: 'updated',
            entityType: Expense::class,
            entityId: $expense->id,
            metadata: [
                'old' => $oldData,
                'new' => [
                    'title' => $expense->title,
                    'amount' => $expense->total_amount,
                    'paid_by' => $expense->paid_by
                ]
            ]
        );
    }

    /**
     * Log a settlement payment
     */
    public function logSettlement(Settlement $settlement): void
    {
        $this->logActivity(
            module: 'settlements',
            action: 'created',
            entityType: Settlement::class,
            entityId: $settlement->id,
            metadata: [
                'amount' => $settlement->amount,
                'from' => $settlement->paid_from,
                'to' => $settlement->paid_to,
                'group_id' => $settlement->group_id
            ]
        );
    }

    /**
     * Log a specific balance change between users
     * This captures the "Ground Truth" shift including before/after states.
     */
    public function logBalanceChange(
        int $groupId,
        int $fromUserId,
        int $toUserId,
        float $amount,
        string $type,
        string $relatedType,
        int $relatedId,
        float $balanceBefore,
        float $balanceAfter
    ): void {
        $this->logFinancialTransaction(
            groupId: $groupId,
            fromUserId: $fromUserId,
            toUserId: $toUserId,
            amount: $amount,
            type: $type,
            relatedType: $relatedType,
            relatedId: $relatedId,
            balanceBefore: $balanceBefore,
            balanceAfter: $balanceAfter
        );
    }

    /**
     * Low-level activity log creation
     */
    public function logActivity(
        string $module,
        string $action,
        string $entityType,
        int $entityId,
        ?array $metadata = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'module' => $module,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Low-level financial log creation (The Financial Ledger)
     */
    public function logFinancialTransaction(
        int $groupId,
        int $fromUserId,
        int $toUserId,
        float $amount,
        string $type,
        string $relatedType,
        int $relatedId,
        ?float $balanceBefore = null,
        ?float $balanceAfter = null,
        ?string $notes = null
    ): FinancialLog {
        return FinancialLog::create([
            'group_id' => $groupId,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'amount' => $amount,
            'type' => $type,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'notes' => $notes,
        ]);
    }

    /**
     * Log authentication events
     */
    public function logAuthEvent(int $userId, string $action, bool $success = true, ?array $metadata = null): AuthLog
    {
        return AuthLog::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'success' => $success,
            'metadata' => $metadata,
        ]);
    }
}
