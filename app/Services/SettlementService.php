<?php

namespace App\Services;

use App\Models\Settlement;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class SettlementService
{
    public function __construct(
        protected BalanceService $balanceService,
        protected LoggingService $loggingService
    ) {}

    /**
     * Create a new settlement
     * 
     * @param array $data ['group_id', 'paid_from', 'paid_to', 'amount', 'payment_mode', 'note']
     * @return Settlement
     */
    public function createSettlement(array $data): Settlement
    {
        // SECURITY: Use FormRequest for strict input validation
        $request = new \App\Http\Requests\CreateSettlementRequest();
        $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());
        $request->withValidator($validator);
        $validated = $validator->validate();

        return DB::transaction(function () use ($validated, $data) { // Use validated for keys, keep logic
            $groupId = $validated['group_id'];
            $fromId = $validated['paid_from'];
            $toId = $validated['paid_to'];
            $amount = (float)$validated['amount'];
            $createdBy = Auth::id() ?? $fromId;

            if ($amount <= 0) {
                throw new InvalidArgumentException("Settlement amount must be greater than zero."); // Redundant but safe
            }

            if ($fromId === $toId) {
                throw new InvalidArgumentException("Cannot settle with yourself."); // Redundant but safe
            }

            // 1. Validate group membership (Already done by FormRequest, keeping for safe measure inside transaction or removing? 
            // FormRequest `withValidator` logic checks this. We can remove the explicit check here to clean up, 
            // or keep it if we fear race conditions (membership changing between validation and transaction? Unlikely in this flow).
            // I will remove the explicit query to save performance as requested.

            // 2. Prevent over-settlement
            // Get how much $fromId owes $toId
            $currentOwed = $this->balanceService->getNetBalanceBetweenUsers($groupId, $fromId, $toId);
            
            // If currentOwed is positive, $fromId owes $toId.
            // If currentOwed is negative, $toId owes $fromId.
            
            if ($currentOwed <= 0) {
                throw new InvalidArgumentException("User does not owe any money to this participant.");
            }

            // SECURITY: Strict validation - NO margin to prevent micro-theft attacks
            if ($amount > $currentOwed) {
                throw new InvalidArgumentException(sprintf(
                    'Settlement amount ($%s) exceeds amount owed ($%s). Maximum allowed: $%s',
                    number_format($amount, 2),
                    number_format($currentOwed, 2),
                    number_format($currentOwed, 2)
                ));
            }
            
            // Validate minimum amount
            if ($amount <= 0) {
                throw new InvalidArgumentException('Settlement amount must be greater than zero.');
            }

            // 3. Create Settlement record
            $settlement = Settlement::create([
                'group_id' => $groupId,
                'paid_from' => $fromId,
                'paid_to' => $toId,
                'amount' => $amount,
                'payment_mode' => $data['payment_mode'] ?? 'cash',
                'note' => $data['note'] ?? null,
                'created_by' => $createdBy,
            ]);

            // 4. Update balances
            $this->balanceService->updateBalancesFromSettlement($settlement);

            // 5. Log Activity
            $this->loggingService->logActivity(
                'settlements',
                'created',
                Settlement::class,
                $settlement->id,
                ['amount' => $amount, 'from' => $fromId, 'to' => $toId]
            );

            return $settlement;
        });
    }

    /**
     * Reverse a settlement (Delete)
     */
    public function reverseSettlement(Settlement $settlement): bool
    {
        return DB::transaction(function () use ($settlement) {
            // Logic to reverse balance shift
            // adjustPairBalance with +amount (since original was -amount)
            // We use the internal balanceService method logic but we need an exposed way to reverse.
            // Actually, we can just call adjustPairBalance but it's protected.
            
            // Let's call a manual adjustment or just delete and let an observer/logic handle it?
            // The user requested a function. 
            
            // We'll use the reverse logic: Add the amount back to the balance.
            // We need to inject the reverse logic into BalanceService or use adjustPairBalance logic here.
            
            // Best approach: Add a 'reverseBalancesFromSettlement' to BalanceService if possible,
            // or perform it here using public methods of BalanceService if available.
            // Since adjustPairBalance is protected, I'll assume we might need a public helper or 
            // just perform the subtraction reversal here if we had access.
            
            // Wait, I can just create a temporary Settlement object or pass negative amount to updateBalancesFromSettlement?
            // No, updateBalancesFromSettlement subtracts. To reverse, we need to ADD.
            
            // Let's simulate a reverse by creating a dummy settlement or adding a reverse method 
            // to BalanceService (better architecture).
            
            // I will first implement it here assuming I can use a similar logic if I add a method to BalanceService.
            // Actually, I'll go ahead and add a reverseBalancesFromSettlement to BalanceService in the same turn.
            
            $this->balanceService->reverseBalancesFromSettlement($settlement);

            // Log activity
            $this->loggingService->logActivity(
                'settlements',
                'deleted',
                Settlement::class,
                $settlement->id,
                ['amount' => $settlement->amount]
            );

            return $settlement->delete();
        });
    }
}
