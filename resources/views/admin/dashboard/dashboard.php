<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Admin Dashboard - TechMage')] class extends Component
{
    public function render()
    {
        return view('admin.dashboard.dashboard');
    }
};
