<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.employee')] #[Title('Employee Settings - TechMage')] class extends Component
{
    public string $loginTime = '09:00';

    public string $lunchStartTime = '13:00';

    public string $lunchEndTime = '14:00';

    public function mount(): void
    {
        $this->loginTime = (string) get_setting('login_time', '09:00', 'employee');
        $this->lunchStartTime = (string) get_setting('lunch_start_time', '13:00', 'employee');
        $this->lunchEndTime = (string) get_setting('lunch_end_time', '14:00', 'employee');
    }

    public function saveSettings(): void
    {
        $this->validate([
            'loginTime' => ['required', 'string'],
            'lunchStartTime' => ['required', 'string'],
            'lunchEndTime' => ['required', 'string'],
        ]);

        set_setting('login_time', $this->loginTime, 'employee');
        set_setting('lunch_start_time', $this->lunchStartTime, 'employee');
        set_setting('lunch_end_time', $this->lunchEndTime, 'employee');

        session()->flash('message', 'Employee schedule settings saved successfully.');
    }

    public function render()
    {
        return view('employee.setting.setting');
    }
};
