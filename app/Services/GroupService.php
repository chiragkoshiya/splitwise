<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class GroupService
{
    public function __construct(
        protected LoggingService $loggingService,
        protected BalanceService $balanceService
    ) {}

    /**
     * Create a new group
     */
    public function createGroup(array $data): Group
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            
            $group = Group::create([
                'name' => $data['name'],
                'icon' => $data['icon'] ?? '👥',
                'created_by' => $user->id,
            ]);

            // Add creator as member
            $group->users()->attach($user->id, ['joined_at' => now()]);

            // If other members passed, add them
            if (isset($data['members']) && is_array($data['members'])) {
                foreach ($data['members'] as $userId) {
                    if ($userId != $user->id) {
                        $this->addMember($group, User::findOrFail($userId));
                    }
                }
            }

            $this->loggingService->logActivity(
                'groups',
                'created',
                Group::class,
                $group->id,
                ['name' => $group->name]
            );

            return $group;
        });
    }

    /**
     * Add member to group
     */
    public function addMember(Group $group, User $user): void
    {
        if ($group->users()->where('users.id', $user->id)->exists()) {
            return;
        }

        $group->users()->attach($user->id, ['joined_at' => now()]);

        $this->loggingService->logActivity(
            'groups',
            'member_added',
            Group::class,
            $group->id,
            ['user_id' => $user->id]
        );
    }

    /**
     * Remove member from group
     * Only allowed if user balance is 0
     */
    public function removeMember(Group $group, User $user): void
    {
        // 1. Check if user has non-zero balances in this group
        $balances = $this->balanceService->getUserBalancesForGroup($group->id, $user->id);
        
        foreach ($balances as $balance) {
            if (abs((float)$balance->amount) > 0.01) {
                throw new InvalidArgumentException("User cannot be removed as they have outstanding balances.");
            }
        }

        $group->users()->detach($user->id);

        $this->loggingService->logActivity(
            'groups',
            'member_removed',
            Group::class,
            $group->id,
            ['user_id' => $user->id]
        );
    }

    /**
     * Delete a group
     * Only allowed if all balances are 0
     */
    public function deleteGroup(Group $group): bool
    {
        return DB::transaction(function () use ($group) {
            // 1. Check all balances in the group
            $hasBalances = $group->balances()->where('amount', '!=', 0)->exists();
            
            if ($hasBalances) {
                throw new InvalidArgumentException("Group cannot be deleted as it has outstanding balances.");
            }

            $this->loggingService->logActivity(
                'groups',
                'deleted',
                Group::class,
                $group->id,
                ['name' => $group->name]
            );

            return $group->delete();
        });
    }
}
