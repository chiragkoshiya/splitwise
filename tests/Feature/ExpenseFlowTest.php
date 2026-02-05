<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Services\ExpenseService;
use App\Services\BalanceService;
use App\Services\LoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseFlowTest extends TestCase
{
    use RefreshDatabase;

    protected ExpenseService $expenseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->expenseService = new ExpenseService(
            new BalanceService(new LoggingService()),
            new LoggingService()
        );
    }

    public function test_it_creates_expense_and_updates_balances_correctly()
    {
        $group = Group::factory()->create();
        $payer = User::factory()->create();
        $friend = User::factory()->create();

        $group->users()->attach([$payer->id, $friend->id]);

        $data = [
            'group_id' => $group->id,
            'paid_by' => $payer->id,
            'title' => 'Pizza Night',
            'total_amount' => 100.00,
            'splits' => [
                ['user_id' => $payer->id, 'share_amount' => 50.00],
                ['user_id' => $friend->id, 'share_amount' => 50.00],
            ]
        ];

        $this->actingAs($payer);

        $expense = $this->expenseService->createExpense($data);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'total_amount' => 100.00
        ]);

        $balanceService = new BalanceService(new LoggingService());
        $balance = $balanceService->getNetBalanceBetweenUsers($group->id, $friend->id, $payer->id);
        
        $this->assertEquals(50.00, $balance);
    }

    public function test_it_reverses_balances_when_expense_is_deleted()
    {
        $group = Group::factory()->create();
        $payer = User::factory()->create();
        $friend = User::factory()->create();
        $group->users()->attach([$payer->id, $friend->id]);
        $this->actingAs($payer);

        $expense = $this->expenseService->createExpense([
            'group_id' => $group->id,
            'paid_by' => $payer->id,
            'title' => 'Movie',
            'total_amount' => 60.00,
            'splits' => [
                ['user_id' => $payer->id, 'share_amount' => 30.00],
                ['user_id' => $friend->id, 'share_amount' => 30.00],
            ]
        ]);

        $this->expenseService->deleteExpense($expense);

        $balanceService = new BalanceService(new LoggingService());
        $balance = $balanceService->getNetBalanceBetweenUsers($group->id, $friend->id, $payer->id);
        
        $this->assertEquals(0.00, $balance);
    }
}
