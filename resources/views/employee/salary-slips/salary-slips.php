<?php

use App\Models\SalaryPayment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.employee')] #[Title('My Salary Slips - TechMage')] class extends Component
{
    use WithPagination;

    public string $yearFilter = '';

    public ?int $selectedId = null;

    public function updatedYearFilter(): void
    {
        $this->resetPage();
    }

    public function viewSlip(int $id): void
    {
        // Only allow the employee's own slips
        $this->selectedId = SalaryPayment::where('user_id', Auth::id())->findOrFail($id)->id;
    }

    public function closeSlip(): void
    {
        $this->selectedId = null;
    }

    public function render()
    {
        $userId = Auth::id();
        $base = SalaryPayment::where('user_id', $userId);

        $years = (clone $base)->orderByDesc('salary_month')->pluck('salary_month')
            ->map(fn ($d) => $d->format('Y'))->unique()->values();

        $slips = (clone $base)
            ->when($this->yearFilter !== '', fn ($q) => $q->whereYear('salary_month', (int) $this->yearFilter))
            ->orderByDesc('salary_month')
            ->paginate(12);

        $latest = (clone $base)->orderByDesc('salary_month')->first();
        $currentYear = now()->year;

        return view('employee.salary-slips.salary-slips', [
            'slips' => $slips,
            'years' => $years,
            'latest' => $latest,
            'totalSlips' => (clone $base)->count(),
            'yearEarnings' => (float) (clone $base)->whereYear('salary_month', $currentYear)->sum('net_salary'),
            'currentYear' => $currentYear,
            'monthlySalary' => (float) Auth::user()->salary,
            'selected' => $this->selectedId ? (clone $base)->find($this->selectedId) : null,
        ]);
    }
};
