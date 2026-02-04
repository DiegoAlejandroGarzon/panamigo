<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $employees = \App\Models\User::role(['Atención al Cliente', 'Cajera'])
            ->withCount('orders') // Assuming relationship orders defined on User
            ->get();

        return view('livewire.admin.dashboard', [
            'employees' => $employees
        ]);
    }
}
