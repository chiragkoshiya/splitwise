<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Expense;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $totalBalance = 0;
    public $youOwe = 0;
    public $youAreOwed = 0;
    public $recentExpenses;
    public $quickGroups;

    public function mount()
    {
        $user = Auth::user();
        
        // Calculate balance summary
        $this->calculateBalances($user);
        
        // Get recent expenses (last 5)
        $this->recentExpenses = Expense::whereHas('group.users', function($q) use ($user) {
            $q->where('users.id', $user->id);
        })
        ->with(['group', 'paidByUser'])
        ->latest()
        ->take(5)
        ->get();
        
        // Get user's groups (first 3)
        $this->quickGroups = $user->groups()
            ->withCount('users')
            ->take(3)
            ->get();
    }
    
    private function calculateBalances($user)
    {
        // Get balances where user owes
        $owes = $user->balancesOwed()->sum('amount');
        
        // Get balances where user is owed
        $owed = $user->balancesOwedTo()->sum('amount');
        
        $this->youOwe = abs($owes);
        $this->youAreOwed = $owed;
        $this->totalBalance = $owed - abs($owes);
    }

    #[Layout('layouts.app', ['title' => 'Dashboard', 'active' => 'dashboard'])]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.dashboard');
    }
}
