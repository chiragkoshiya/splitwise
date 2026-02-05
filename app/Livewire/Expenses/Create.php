<?php

namespace App\Livewire\Expenses;

use App\Models\Group;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public Group $group;
    
    public $title;
    public $amount;
    public $paid_by;
    public $selected_members = []; // IDs of members involved in split

    protected $rules = [
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'paid_by' => 'required|exists:users,id',
        'selected_members' => 'required|array|min:1',
    ];

    public function mount(Group $group)
    {
        $this->authorize('view', $group);
        $this->group = $group;
        $this->paid_by = Auth::id();
        
        // Default: everyone in group is selected
        $this->selected_members = $this->group->users->pluck('id')->toArray();
    }

    /**
     * Create the expense
     */
    public function save(ExpenseService $expenseService)
    {
        $this->validate();

        $count = count($this->selected_members);
        $shareAmount = round($this->amount / $count, 2);
        
        // Prepare splits (Equal split implementation)
        $splits = [];
        $runningTotal = 0;

        foreach ($this->selected_members as $index => $userId) {
            // Adjust the last person's share to account for rounding errors
            if ($index === $count - 1) {
                $userShare = $this->amount - $runningTotal;
            } else {
                $userShare = $shareAmount;
                $runningTotal += $userShare;
            }

            $splits[] = [
                'user_id' => $userId,
                'share_amount' => $userShare
            ];
        }

        try {
            $expenseService->createExpense([
                'group_id' => $this->group->id,
                'title' => $this->title,
                'total_amount' => $this->amount,
                'paid_by' => $this->paid_by,
                'splits' => $splits
            ]);

            session()->flash('message', 'Expense added successfully.');
            return redirect()->route('groups.show', $this->group->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[Layout('layouts.app', ['title' => 'Add Expense', 'active' => 'groups', 'back' => true])]
    #[Title('Add Expense')]
    public function render()
    {
        return view('livewire.expenses.create', [
            'members' => $this->group->users
        ]);
    }
}
