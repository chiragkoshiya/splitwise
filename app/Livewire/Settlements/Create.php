<?php

namespace App\Livewire\Settlements;

use App\Livewire\SecureComponent;
use App\Models\Group;
use App\Models\User;
use App\Services\SettlementService;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Create extends SecureComponent
{
    public Group $group;
    public $groupId;
    
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

    protected function authorizeAccess(): void
    {
        // Load group
        $this->group = Group::findOrFail($this->groupId);
        
        // SECURITY: Verify group membership
        if (!$this->group->users()->where('users.id', auth()->id())->exists()) {
            abort(403, 'You are not a member of this group.');
        }
        
        // Verify user can create settlements
        $this->authorize('create-settlement', $this->group);
    }

    protected function secureMount(...$params): void
    {
        $this->groupId = $params[0] ?? null;
        $this->paid_from = Auth::id();
        
        // Try to find someone the user owes to pre-fill
        $balanceService = app(BalanceService::class);
        $balances = $balanceService->getUserBalancesForGroup($this->group->id, Auth::id());
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
