<?php

use App\Models\SalaryPayment;
use App\Models\User;
use App\Services\SalaryCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Salary Management - TechMage')] class extends Component
{
    use WithPagination;

    public string $tab = 'payroll'; // payroll | history

    public string $selectedMonth = '';

    public string $search = '';

    public string $historyMonth = '';

    // Modal state
    public bool $showSalaryModal = false;

    public ?int $employeeId = null;

    public array $breakdown = [];

    public ?int $paymentId = null;

    // Edit mode for an already paid salary
    public bool $editingPayment = false;

    public bool $recalculate = false;

    // Payment form
    public string|float|null $bonus = 0;

    public string|float|null $deduction = 0;

    public string $payment_method = 'bank_transfer';

    public string $payment_date = '';

    public string $notes = '';

    public function mount(): void
    {
        // Default to the previous month, which is the usual payroll month
        $this->selectedMonth = now()->subMonthNoOverflow()->format('Y-m');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedHistoryMonth(): void
    {
        $this->resetPage();
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    private function monthParts(): array
    {
        try {
            $date = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth();
        } catch (\Throwable) {
            $date = now()->startOfMonth();
            $this->selectedMonth = $date->format('Y-m');
        }

        return [(int) $date->year, (int) $date->month];
    }

    public function openSalaryModal(int $userId): void
    {
        $this->resetErrorBag();
        [$year, $month] = $this->monthParts();

        $employee = User::findOrFail($userId);
        $this->employeeId = $employee->id;

        $payment = SalaryPayment::where('user_id', $employee->id)
            ->whereDate('salary_month', Carbon::create($year, $month, 1)->toDateString())
            ->first();

        $this->paymentId = $payment?->id;
        $this->breakdown = app(SalaryCalculator::class)->calculate($employee, $year, $month);
        $this->editingPayment = false;
        $this->recalculate = false;

        $this->fillPaymentForm($payment);

        $this->showSalaryModal = true;
    }

    private function fillPaymentForm(?SalaryPayment $payment): void
    {
        $this->bonus = $payment ? (float) $payment->bonus : 0;
        $this->deduction = $payment ? (float) $payment->deduction : 0;
        $this->payment_method = $payment?->payment_method ?? 'bank_transfer';
        $this->payment_date = $payment?->payment_date?->toDateString() ?? now()->toDateString();
        $this->notes = $payment?->notes ?? '';
    }

    public function viewPayment(int $id): void
    {
        $payment = SalaryPayment::findOrFail($id);
        $this->selectedMonth = $payment->salary_month->format('Y-m');
        $this->openSalaryModal($payment->user_id);
    }

    public function editPayment(int $id): void
    {
        $this->viewPayment($id);
        $this->startEditPayment();
    }

    public function startEditPayment(): void
    {
        if (! $this->paymentId) {
            return;
        }

        $this->resetErrorBag();
        $this->fillPaymentForm(SalaryPayment::find($this->paymentId));
        $this->recalculate = false;
        $this->editingPayment = true;
    }

    public function cancelEditPayment(): void
    {
        $this->resetErrorBag();
        $this->fillPaymentForm($this->paymentId ? SalaryPayment::find($this->paymentId) : null);
        $this->recalculate = false;
        $this->editingPayment = false;
    }

    public function updatePayment(): void
    {
        if (! $this->paymentId || ! $this->editingPayment) {
            return;
        }

        $validated = $this->validate([
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recalculate' => ['boolean'],
        ]);

        $payment = SalaryPayment::with('user')->findOrFail($this->paymentId);
        $data = [];

        // Optionally refresh the attendance snapshot (e.g. after attendance was corrected)
        if ($this->recalculate && $payment->user) {
            $calc = app(SalaryCalculator::class)->calculate(
                $payment->user,
                (int) $payment->salary_month->year,
                (int) $payment->salary_month->month,
            );

            $data = [
                'monthly_salary' => $calc['monthly_salary'],
                'total_days' => $calc['total_days'],
                'week_off_days' => $calc['week_off_days'],
                'holiday_days' => $calc['holiday_days'],
                'working_days' => $calc['working_days'],
                'present_days' => $calc['present_days'],
                'paid_leave_days' => $calc['paid_leave_days'],
                'unpaid_leave_days' => $calc['unpaid_leave_days'],
                'absent_days' => $calc['absent_days'] + $calc['upcoming_days'],
                'payable_days' => $calc['payable_days'],
                'per_day_salary' => $calc['per_day_salary'],
                'calculated_salary' => $calc['calculated_salary'],
            ];
        }

        $calculated = (float) ($data['calculated_salary'] ?? $payment->calculated_salary);
        $bonus = (float) ($validated['bonus'] ?: 0);
        $deduction = (float) ($validated['deduction'] ?: 0);
        $net = max(0, round($calculated + $bonus - $deduction, 2));

        $payment->update($data + [
            'bonus' => $bonus,
            'deduction' => $deduction,
            'net_salary' => $net,
            'payment_method' => 'bank_transfer', // salaries are always paid by bank transfer
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?: null,
        ]);

        $this->editingPayment = false;
        $this->recalculate = false;

        $this->dispatch('toast-show', [
            'message' => 'Salary payment updated. Net salary: ₹'.number_format($net, 2).'.',
            'type' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function closeSalaryModal(): void
    {
        $this->showSalaryModal = false;
        $this->editingPayment = false;
        $this->recalculate = false;
        $this->employeeId = null;
        $this->paymentId = null;
        $this->breakdown = [];
        $this->resetErrorBag();
    }

    public function paySalary(): void
    {
        if (! $this->employeeId || $this->paymentId) {
            return;
        }

        $validated = $this->validate([
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        [$year, $month] = $this->monthParts();
        $employee = User::findOrFail($this->employeeId);

        if ((float) $employee->salary <= 0) {
            $this->addError('bonus', 'Set a monthly salary for this employee in Employee Management first.');

            return;
        }

        $salaryMonth = Carbon::create($year, $month, 1)->toDateString();

        if (SalaryPayment::where('user_id', $employee->id)->whereDate('salary_month', $salaryMonth)->exists()) {
            $this->dispatch('toast-show', [
                'message' => 'Salary for this month is already paid.',
                'type' => 'danger',
                'position' => 'top-right',
            ]);
            $this->closeSalaryModal();

            return;
        }

        // Always recalculate on the server at the moment of payment
        $calc = app(SalaryCalculator::class)->calculate($employee, $year, $month);

        $bonus = (float) ($validated['bonus'] ?: 0);
        $deduction = (float) ($validated['deduction'] ?: 0);
        $net = max(0, round($calc['calculated_salary'] + $bonus - $deduction, 2));

        SalaryPayment::create([
            'user_id' => $employee->id,
            'salary_month' => $salaryMonth,
            'monthly_salary' => $calc['monthly_salary'],
            'total_days' => $calc['total_days'],
            'week_off_days' => $calc['week_off_days'],
            'holiday_days' => $calc['holiday_days'],
            'working_days' => $calc['working_days'],
            'present_days' => $calc['present_days'],
            'paid_leave_days' => $calc['paid_leave_days'],
            'unpaid_leave_days' => $calc['unpaid_leave_days'],
            'absent_days' => $calc['absent_days'] + $calc['upcoming_days'],
            'payable_days' => $calc['payable_days'],
            'per_day_salary' => $calc['per_day_salary'],
            'calculated_salary' => $calc['calculated_salary'],
            'bonus' => $bonus,
            'deduction' => $deduction,
            'net_salary' => $net,
            'payment_method' => 'bank_transfer', // salaries are always paid by bank transfer
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?: null,
            'paid_by' => Auth::id(),
        ]);

        $this->dispatch('toast-show', [
            'message' => 'Salary of ₹'.number_format($net, 2).' paid to '.$employee->name.'.',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        $this->closeSalaryModal();
    }

    public function deletePayment(int $id): void
    {
        SalaryPayment::findOrFail($id)->delete();

        $this->dispatch('toast-show', [
            'message' => 'Salary payment record removed. You can pay it again.',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        if ($this->paymentId === $id) {
            $this->closeSalaryModal();
        }
    }

    public function render()
    {
        [$year, $month] = $this->monthParts();
        $salaryMonth = Carbon::create($year, $month, 1);
        $search = trim($this->search);

        $employees = collect();
        $history = collect();

        if ($this->tab === 'history') {
            $history = SalaryPayment::with(['user', 'payer'])
                ->when($this->historyMonth !== '', function ($q) {
                    try {
                        $m = Carbon::createFromFormat('Y-m', $this->historyMonth)->startOfMonth();
                        $q->whereDate('salary_month', $m->toDateString());
                    } catch (\Throwable) {
                    }
                })
                ->when($search !== '', fn ($q) => $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
                }))
                ->orderByDesc('salary_month')
                ->orderByDesc('id')
                ->paginate(10);
        } else {
            $employees = User::with(['department', 'salaryPayments' => fn ($q) => $q->whereDate('salary_month', $salaryMonth->toDateString())])
                ->where('role', 'employee')
                ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
                }))
                ->orderBy('name')
                ->paginate(10);
        }

        $monthPayments = SalaryPayment::whereDate('salary_month', $salaryMonth->toDateString());

        $stats = [
            'employees' => User::where('role', 'employee')->where('is_active', true)->count(),
            'monthly_payroll' => (float) User::where('role', 'employee')->where('is_active', true)->sum('salary'),
            'paid_count' => (clone $monthPayments)->count(),
            'paid_total' => (float) (clone $monthPayments)->sum('net_salary'),
        ];

        $selectedEmployee = $this->employeeId ? User::with('department')->find($this->employeeId) : null;
        $selectedPayment = $this->paymentId ? SalaryPayment::with('payer')->find($this->paymentId) : null;

        return view('admin.salary-management.salary-management', [
            'employees' => $employees,
            'history' => $history,
            'stats' => $stats,
            'monthLabel' => $salaryMonth->format('F Y'),
            'selectedEmployee' => $selectedEmployee,
            'selectedPayment' => $selectedPayment,
        ]);
    }
};
