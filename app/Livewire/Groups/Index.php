<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    /**
     * Delete a group
     */
    public function delete(int $groupId, GroupService $groupService)
    {
        $group = Group::findOrFail($groupId);
        
        $this->authorize('delete', $group);

        try {
            $groupService->deleteGroup($group);
            session()->flash('message', 'Group deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[Layout('layouts.app', ['title' => 'Groups', 'active' => 'groups'])]
    #[Title('Groups')]
    public function render()
    {
        $groups = Auth::user()->groups()
            ->withCount('users')
            ->latest()
            ->get();

        return view('livewire.groups.index', [
            'groups' => $groups
        ]);
    }
}
