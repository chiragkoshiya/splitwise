<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Expense;
use App\Models\Group;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $totalBalance = 0;
    public $youOwe = 0;
    public $youAreOwed = 0;
    public $recentExpenses;
    public $quickGroups;

    public function mount(BalanceService $balanceService)
    {
        $user = Auth::user();
        
        // Calculate balance summary via service
        $summary = $balanceService->getUserBalanceSummary($user->id);
        $this->totalBalance = $summary['total'];
        $this->youOwe = $summary['owe'];
        $this->youAreOwed = $summary['owed'];
        
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

    #[Layout('layouts.app', ['title' => 'Dashboard', 'active' => 'dashboard'])]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.dashboard');
    }
}
