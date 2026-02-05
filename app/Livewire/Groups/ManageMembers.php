<?php

namespace App\Livewire\Groups;

use App\Livewire\SecureComponent;
use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class ManageMembers extends SecureComponent
{
    public Group $group;
    public $groupId;
    public $email;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    protected function authorizeAccess(): void
    {
        $this->group = Group::findOrFail($this->groupId);
        
        // SECURITY: Verify group membership
        if (!$this->group->users()->where('users.id', auth()->id())->exists()) {
            abort(403, 'You are not a member of this group.');
        }
        
        // SECURITY: Only group admin can manage members
        $this->authorize('update', $this->group);
    }

    protected function secureMount(...$params): void
    {
        $this->groupId = $params[0] ?? null;
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
