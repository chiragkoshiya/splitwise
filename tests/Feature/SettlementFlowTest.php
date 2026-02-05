<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use App\Models\Balance;
use App\Services\SettlementService;
use App\Services\BalanceService;
use App\Services\LoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettlementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected SettlementService $settlementService;
    protected BalanceService $balanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $logger = new LoggingService();
        $this->balanceService = new BalanceService($logger);
        $this->settlementService = new SettlementService(
            $this->balanceService,
            $logger
        );
    }

    public function test_it_records_settlement_and_reduces_balance()
    {
        $group = Group::factory()->create();
        $payer = User::factory()->create();
        $receiver = User::factory()->create();
        $group->users()->attach([$payer->id, $receiver->id]);

        $this->setupDebt($group->id, $payer->id, $receiver->id, 100.00);

        $this->actingAs($payer);
        $this->settlementService->createSettlement([
            'group_id' => $group->id,
            'paid_from' => $payer->id,
            'paid_to' => $receiver->id,
            'amount' => 60.00,
            'payment_mode' => 'cash'
        ]);

        $balance = $this->balanceService->getNetBalanceBetweenUsers($group->id, $payer->id, $receiver->id);
        $this->assertEquals(40.00, $balance);
    }

    public function test_it_prevents_over_settlement()
    {
        $this->expectException(\InvalidArgumentException::class);

        $group = Group::factory()->create();
        $payer = User::factory()->create();
        $receiver = User::factory()->create();
        $group->users()->attach([$payer->id, $receiver->id]);

        $this->setupDebt($group->id, $payer->id, $receiver->id, 50.00);

        $this->actingAs($payer);
        $this->settlementService->createSettlement([
            'group_id' => $group->id,
            'paid_from' => $payer->id,
            'paid_to' => $receiver->id,
            'amount' => 60.00,
        ]);
    }

    protected function setupDebt($groupId, $debtorId, $creditorId, $amount)
    {
        $u1 = min($debtorId, $creditorId);
        $u2 = max($debtorId, $creditorId);
        $actualAmount = ($u1 === $debtorId) ? $amount : -$amount;

        Balance::create([
            'group_id' => $groupId,
            'from_user_id' => $u1,
            'to_user_id' => $u2,
            'amount' => $actualAmount
        ]);
    }
}
