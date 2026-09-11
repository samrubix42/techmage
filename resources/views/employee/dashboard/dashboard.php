<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.employee')] #[Title('Employee Portal - TechMage')] class extends Component
{
    public function render()
    {
        return view('employee.dashboard.dashboard');
    }
};
