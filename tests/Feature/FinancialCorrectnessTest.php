<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use App\Services\ExpenseService;
use App\Services\BalanceService;
use App\Services\LoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialCorrectnessTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_handles_rounding_errors_in_equal_splits()
    {
        $group = Group::factory()->create();
        $payer = User::factory()->create();
        $u2 = User::factory()->create();
        $u3 = User::factory()->create();
        $group->users()->attach([$payer->id, $u2->id, $u3->id]);
        $this->actingAs($payer);

        $logger = new LoggingService();
        $balanceService = new BalanceService($logger);
        $expenseService = new ExpenseService($balanceService, $logger);

        $amount = 10.00;
        
        $splits = [
            ['user_id' => $payer->id, 'share_amount' => 3.33],
            ['user_id' => $u2->id, 'share_amount' => 3.33],
            ['user_id' => $u3->id, 'share_amount' => 3.34],
        ];

        $expenseService->createExpense([
            'group_id' => $group->id,
            'paid_by' => $payer->id,
            'title' => 'Tricky Split',
            'total_amount' => $amount,
            'splits' => $splits
        ]);

        $this->assertEquals($amount, array_sum(array_column($splits, 'share_amount')));

        $this->assertEquals(3.33, $balanceService->getNetBalanceBetweenUsers($group->id, $u2->id, $payer->id));
        $this->assertEquals(3.34, $balanceService->getNetBalanceBetweenUsers($group->id, $u3->id, $payer->id));
        
        $summary = $balanceService->getUserBalanceSummary($payer->id);
        $this->assertEquals(6.67, $summary['owed']);
    }
}
