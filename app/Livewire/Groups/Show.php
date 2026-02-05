<?php

namespace App\Livewire\Groups;

use App\Livewire\SecureComponent;
use App\Models\Group;
use App\Models\Expense;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Show extends SecureComponent
{
    public Group $group;
    public $groupId;

    protected function authorizeAccess(): void
    {
        // Load group first
        $this->group = Group::findOrFail($this->groupId);
        
        // SECURITY: Verify group membership
        if (!$this->group->users()->where('users.id', auth()->id())->exists()) {
            abort(403, 'You are not a member of this group.');
        }
        
        // Verify policy
        $this->authorize('view', $this->group);
    }

    protected function secureMount(...$params): void
    {
        $this->groupId = $params[0] ?? null;
        // Group already loaded in authorizeAccess
    }

    #[Layout('layouts.app', ['title' => 'Group Details', 'active' => 'groups', 'back' => true])]
    #[Title('Group Details')]
    public function render(BalanceService $balanceService)
    {
        $user = Auth::user();
        
        // Fetch group expenses
        $expenses = $this->group->expenses()
            ->with(['paidByUser', 'splits'])
            ->latest()
            ->get();

        // Fetch user balances in this group
        $balances = $balanceService->getUserBalancesForGroup($this->group->id, $user->id);

        return view('livewire.groups.show', [
            'expenses' => $expenses,
            'balances' => $balances
        ]);
    }
}
