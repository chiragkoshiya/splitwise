<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ManageMembers extends Component
{
    public Group $group;
    public $email;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    public function mount(Group $group)
    {
        $this->authorize('view', $group);
        $this->group = $group;
    }

    public function addMember(GroupService $groupService)
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        try {
            $groupService->addMember($this->group, $user);
            session()->flash('message', 'Member added successfully.');
            $this->email = '';
            $this->group->load('users');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeMember(int $userId, GroupService $groupService)
    {
        $user = User::findOrFail($userId);
        
        try {
            $groupService->removeMember($this->group, $user);
            session()->flash('message', 'Member removed successfully.');
            $this->group->load('users');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[Layout('layouts.app', ['title' => 'Group Members', 'active' => 'groups', 'back' => true])]
    #[Title('Group Members')]
    public function render()
    {
        return view('livewire.groups.manage-members', [
            'members' => $this->group->users
        ]);
    }
}
