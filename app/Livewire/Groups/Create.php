<?php

namespace App\Livewire\Groups;

use App\Services\GroupService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public $name;
    public $icon = '👥';

    protected $rules = [
        'name' => 'required|string|max:255|min:3',
        'icon' => 'required|string',
    ];

    public function save(GroupService $groupService)
    {
        $this->validate();

        try {
            $group = $groupService->createGroup([
                'name' => $this->name,
                'icon' => $this->icon,
            ]);

            session()->flash('message', 'Group created successfully!');
            return redirect()->route('groups.show', $group->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[Layout('layouts.app', ['title' => 'Create Group', 'active' => 'groups', 'back' => true])]
    #[Title('Create Group')]
    public function render()
    {
        return view('livewire.groups.create');
    }
}
