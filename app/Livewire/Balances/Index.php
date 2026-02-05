<?php

namespace App\Livewire\Balances;

use App\Models\Group;
use App\Services\BalanceService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    public Group $group;

    public function mount(Group $group)
    {
        $this->authorize('view', $group);
        $this->group = $group;
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
