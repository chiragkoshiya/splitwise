<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\Expense;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Show extends Component
{
    public Group $group;

    public function mount(Group $group)
    {
        $this->authorize('view', $group);
        $this->group = $group;
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
