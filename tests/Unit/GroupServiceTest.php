<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\User;
use App\Models\Balance;
use App\Services\GroupService;
use App\Services\BalanceService;
use App\Services\LoggingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GroupService $groupService;

    protected function setUp(): void
    {
        parent::setUp();
        $logger = new LoggingService();
        $this->groupService = new GroupService(
            $logger,
            new BalanceService($logger)
        );
    }

    public function test_it_creates_group_and_adds_creator_as_member()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $group = $this->groupService->createGroup(['name' => 'Europe Trip']);

        $this->assertDatabaseHas('groups', ['name' => 'Europe Trip', 'created_by' => $user->id]);
        $this->assertTrue($group->users()->where('users.id', $user->id)->exists());
    }

    public function test_it_prevents_deleting_group_with_active_balances()
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = User::factory()->create();
        $group = Group::factory()->create(['created_by' => $user->id]);
        
        Balance::create([
            'group_id' => $group->id,
            'from_user_id' => $user->id,
            'to_user_id' => User::factory()->create()->id,
            'amount' => 100
        ]);

        $this->groupService->deleteGroup($group);
    }
}
