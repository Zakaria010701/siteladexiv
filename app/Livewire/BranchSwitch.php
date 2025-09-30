<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Component;

class BranchSwitch extends Component
{
    public $label;

    public $options;

    public User $user;

    public function mount()
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if(empty($user->currentBranch)) {
            $user->current_branch_id = $user->branches()->first()?->id ?? Branch::first()?->id;
            $user->save();
        }

        $this->user = $user;
        $this->label = $user->currentBranch?->name ?? __('Choose Branch');
        $this->options = $user->branches->pluck('name', 'id');
    }

    public function render()
    {
        return view('livewire.branch-switch');
    }

    public function switch(int $key, string $label)
    {
        $this->label = $label;
        $this->user->current_branch_id = $key;
        $this->user->save();

        $this->dispatch('switched-branch', branch: $key);
    }
}
