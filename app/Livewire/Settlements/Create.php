<?php

namespace App\Livewire\Settlements;

use App\Models\Group;
use App\Models\User;
use App\Services\SettlementService;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public Group $group;
    
    public $paid_from;
    public $paid_to;
    public $amount;
    public $payment_mode = 'cash';
    public $note;

    protected $rules = [
        'paid_from' => 'required|exists:users,id',
        'paid_to' => 'required|exists:users,id',
        'amount' => 'required|numeric|min:0.01',
        'payment_mode' => 'required|string',
        'note' => 'nullable|string|max:255',
    ];

    public function mount(Group $group, BalanceService $balanceService)
    {
        $this->authorize('view', $group);
        $this->group = $group;
        $this->paid_from = Auth::id();
        
        // Try to find someone the user owes to pre-fill
        $balances = $balanceService->getUserBalancesForGroup($group->id, Auth::id());
        foreach ($balances as $balance) {
            $u1 = $balance->from_user_id;
            $currentUserId = Auth::id();
            $amount = ($u1 === $currentUserId) ? $balance->amount : -$balance->amount;
            
            if ($amount > 0) {
                $this->paid_to = ($u1 === $currentUserId) ? $balance->to_user_id : $balance->from_user_id;
                $this->amount = abs($amount);
                break;
            }
        }
    }

    public function save(SettlementService $settlementService)
    {
        $this->validate();

        try {
            $settlementService->createSettlement([
                'group_id' => $this->group->id,
                'paid_from' => $this->paid_from,
                'paid_to' => $this->paid_to,
                'amount' => $this->amount,
                'payment_mode' => $this->payment_mode,
                'note' => $this->note,
            ]);

            session()->flash('message', 'Settlement recorded successfully.');
            return redirect()->route('groups.show', $this->group->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[Layout('layouts.app', ['title' => 'Settle Up', 'active' => 'groups', 'back' => true])]
    #[Title('Settle Up')]
    public function render()
    {
        return view('livewire.settlements.create', [
            'members' => $this->group->users
        ]);
    }
}
