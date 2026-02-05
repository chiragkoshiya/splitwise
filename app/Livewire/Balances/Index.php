<?php

namespace App\Livewire\Balances;

use App\Livewire\SecureComponent;
use App\Models\Group;
use App\Services\BalanceService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Index extends SecureComponent
{
    public Group $group;
    public $groupId;

    protected function authorizeAccess(): void
    {
        $this->group = Group::findOrFail($this->groupId);
        
        // SECURITY: Verify group membership
        if (!$this->group->users()->where('users.id', auth()->id())->exists()) {
            abort(403, 'You are not a member of this group.');
        }
        
        $this->authorize('view', $this->group);
    }

    protected function secureMount(...$params): void
    {
        $this->groupId = $params[0] ?? null;
    }

    #[Layout('layouts.app', ['title' => 'Group Balances', 'active' => 'groups', 'back' => true])]
    #[Title('Group Balances')]
    public function render(BalanceService $balanceService)
    {
        $allBalances = $balanceService->getGroupBalances($this->group->id);

        return view('livewire.balances.index', [
            'balances' => $allBalances
        ]);
    }
}
