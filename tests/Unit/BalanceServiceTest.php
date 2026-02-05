<?php

namespace Tests\Unit;

use App\Models\Balance;
use App\Models\Group;
use App\Models\User;
use App\Services\BalanceService;
use App\Services\LoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BalanceService $balanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->balanceService = new BalanceService(new LoggingService());
    }

    public function test_it_normalizes_user_pairs_correctly()
    {
        $group = Group::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $balance = $this->balanceService->getNetBalanceBetweenUsers($group->id, $user1->id, $user2->id);
        $this->assertEquals(0, $balance);
    }

    public function test_it_calculates_user_balance_summary_correctly()
    {
        $user = User::factory()->create();
        $other1 = User::factory()->create();
        $other2 = User::factory()->create();
        $group = Group::factory()->create();

        $this->createBalanceRecord($group->id, $user->id, $other1->id, 30);
        $this->createBalanceRecord($group->id, $other2->id, $user->id, 50);

        $summary = $this->balanceService->getUserBalanceSummary($user->id);

        $this->assertEquals(20, $summary['total']); // 50 - 30
        $this->assertEquals(30, $summary['owe']);
        $this->assertEquals(50, $summary['owed']);
    }

    protected function createBalanceRecord($groupId, $debtorId, $creditorId, $amount)
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
