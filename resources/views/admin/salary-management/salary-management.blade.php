@php
    $money = fn ($v) => '₹'.number_format((float) $v, 2);
    $days = fn ($v) => rtrim(rtrim(number_format((float) $v, 1), '0'), '.');
@endphp

<div class="space-y-6" @keydown.escape.window="$wire.closeSalaryModal()">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Admin Control
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Salary Management</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Calculate monthly salary from attendance, holidays, week offs and leaves, then pay and keep records.</p>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Salary Month:</label>
            <input
                type="month"
                wire:model.live="selectedMonth"
                max="{{ now()->format('Y-m') }}"
                class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
            />
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-md p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active Employees</p>
            <p class="text-xl font-bold text-slate-900 mt-1">{{ $stats['employees'] }}</p>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-md p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Monthly Payroll (Fixed)</p>
            <p class="text-xl font-bold text-slate-900 mt-1">{{ $money($stats['monthly_payroll']) }}</p>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-md p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Paid in {{ $monthLabel }}</p>
            <p class="text-xl font-bold text-emerald-700 mt-1">{{ $stats['paid_count'] }} <span class="text-sm font-medium text-slate-500">/ {{ $stats['employees'] }}</span></p>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-md p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Paid ({{ $monthLabel }})</p>
            <p class="text-xl font-bold text-emerald-700 mt-1">{{ $money($stats['paid_total']) }}</p>
        </div>
    </div>

    <!-- Tabs + Search -->
    <div class="bg-white border border-slate-200/80 rounded-md p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="inline-flex rounded-md border border-slate-200 bg-slate-50 p-1">
            <button
                wire:click="$set('tab', 'payroll')"
                class="px-4 py-1.5 rounded text-xs font-semibold cursor-pointer transition-all {{ $tab === 'payroll' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                <i class="ri-money-rupee-circle-line"></i> Payroll — {{ $monthLabel }}
            </button>
            <button
                wire:click="$set('tab', 'history')"
                class="px-4 py-1.5 rounded text-xs font-semibold cursor-pointer transition-all {{ $tab === 'history' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                <i class="ri-history-line"></i> Payment History
            </button>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 md:items-center">
            @if($tab === 'history')
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Month:</label>
                    <input type="month" wire:model.live="historyMonth" class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white" />
                    @if($historyMonth !== '')
                        <button wire:click="$set('historyMonth', '')" class="text-xs text-slate-500 hover:text-slate-800 cursor-pointer">Clear</button>
                    @endif
                </div>
            @endif
            <div class="relative w-full sm:w-72">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search employee..."
                    class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"
                />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200/80 rounded-md shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            @if($tab === 'payroll')
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="text-xs uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Employee</th>
                            <th class="px-6 py-3.5 font-semibold">Department</th>
                            <th class="px-6 py-3.5 font-semibold">Monthly Salary</th>
                            <th class="px-6 py-3.5 font-semibold">Status ({{ $monthLabel }})</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($employees as $employee)
                            @php $paid = $employee->salaryPayments->first(); @endphp
                            <tr wire:key="pay-emp-{{ $employee->id }}" class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-md bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-xs text-slate-700 shrink-0">
                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $employee->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $employee->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $employee->department?->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                    @if((float) $employee->salary > 0)
                                        {{ $money($employee->salary) }}
                                    @else
                                        <span class="text-xs font-medium text-rose-600">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($paid)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="ri-checkbox-circle-line"></i> Paid {{ $money($paid->net_salary) }}
                                        </span>
                                        <p class="text-[11px] text-slate-500 mt-1">on {{ $paid->payment_date->format('M d, Y') }}</p>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="ri-time-line"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        wire:click="openSalaryModal({{ $employee->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold cursor-pointer transition-all {{ $paid ? 'text-slate-700 bg-white border border-slate-300 hover:bg-slate-100' : 'text-white bg-slate-900 hover:bg-slate-800' }}"
                                    >
                                        <i class="{{ $paid ? 'ri-eye-line' : 'ri-calculator-line' }}"></i>
                                        {{ $paid ? 'View Slip' : 'Calculate & Pay' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($employees->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $employees->links() }}</div>
                @endif
            @else
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="text-xs uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Employee</th>
                            <th class="px-6 py-3.5 font-semibold">Month</th>
                            <th class="px-6 py-3.5 font-semibold">Working / Payable Days</th>
                            <th class="px-6 py-3.5 font-semibold">Net Paid</th>
                            <th class="px-6 py-3.5 font-semibold">Payment</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($history as $record)
                            <tr wire:key="pay-rec-{{ $record->id }}" class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $record->user?->name ?? 'Deleted user' }}</p>
                                    <p class="text-xs text-slate-500">{{ $record->user?->email }}</p>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold whitespace-nowrap">{{ $record->salary_month->format('F Y') }}</td>
                                <td class="px-6 py-4 text-xs">{{ $record->working_days }} / {{ $days($record->payable_days) }}</td>
                                <td class="px-6 py-4 font-bold text-emerald-700 whitespace-nowrap">{{ $money($record->net_salary) }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <p class="font-medium text-slate-800">Bank Transfer</p>
                                    <p class="text-slate-500">{{ $record->payment_date->format('M d, Y') }} @if($record->payer) · by {{ $record->payer->name }} @endif</p>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($record->user)
                                        <button
                                            wire:click="viewPayment({{ $record->id }})"
                                            class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-md cursor-pointer"
                                            title="View Slip"
                                        >
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button
                                            wire:click="editPayment({{ $record->id }})"
                                            class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md cursor-pointer"
                                            title="Edit Payment"
                                        >
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <a
                                            href="{{ route('salary-slips.download', $record) }}"
                                            class="inline-block p-1.5 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-md"
                                            title="Download Salary Slip (PDF)"
                                        >
                                            <i class="ri-download-2-line"></i>
                                        </a>
                                    @endif
                                    <button
                                        wire:click="deletePayment({{ $record->id }})"
                                        wire:confirm="Delete this salary payment record? The month will be marked as pending again."
                                        class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-md cursor-pointer"
                                        title="Delete Record"
                                    >
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">No salary payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($history->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $history->links() }}</div>
                @endif
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SALARY CALCULATION / PAY MODAL             -->
    <!-- ========================================== -->
    <div
        x-show="$wire.showSalaryModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div wire:click="closeSalaryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-3xl overflow-hidden rounded-md bg-white text-left shadow-xl border border-slate-200">
                @if($selectedEmployee && !empty($breakdown))
                    @php $b = $breakdown; @endphp

                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($selectedEmployee->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">{{ $selectedEmployee->name }}</h3>
                                <p class="text-xs text-slate-500">
                                    Salary for <span class="font-semibold text-slate-700">{{ $b['month_label'] }}</span>
                                    · {{ $selectedEmployee->department?->name ?? 'Unassigned' }}
                                    · {{ $selectedEmployee->saturday_off_policy === 'sunday_2nd_4th_saturday' ? 'Sunday + 2nd/4th Sat off' : 'Sunday off' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeSalaryModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                        @if($selectedPayment)
                            <div class="flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                                <i class="ri-checkbox-circle-fill text-base"></i>
                                Paid {{ $money($selectedPayment->net_salary) }} on {{ $selectedPayment->payment_date->format('M d, Y') }}
                                via Bank Transfer
                                @if($selectedPayment->payer) · by {{ $selectedPayment->payer->name }} @endif
                            </div>
                        @endif

                        @if((float) $b['monthly_salary'] <= 0)
                            <div class="px-3 py-2 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                                Monthly salary is not set for this employee. Set it in Employee Management before paying.
                            </div>
                        @endif

                        <!-- Days breakdown -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Days Breakdown</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach([
                                    ['Total Days in Month', $b['total_days'], 'bg-slate-50 border-slate-200 text-slate-900'],
                                    ['Week Off Days', $b['week_off_days'], 'bg-slate-50 border-slate-200 text-slate-900'],
                                    ['Holidays', $b['holiday_days'], 'bg-purple-50 border-purple-200 text-purple-800'],
                                    ['Total Working Days', $b['working_days'], 'bg-blue-50 border-blue-200 text-blue-800'],
                                    ['Present Days', $days($b['present_days']), 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                                    ['Leave Days', $days($b['leave_days']), 'bg-amber-50 border-amber-200 text-amber-800'],
                                    ['Absent Days', $days($b['absent_days']), 'bg-rose-50 border-rose-200 text-rose-800'],
                                    ['Payable Days', $days($b['payable_days']), 'bg-slate-900 border-slate-900 text-white'],
                                ] as [$label, $value, $classes])
                                    <div class="rounded-md border p-3 {{ $classes }}">
                                        <p class="text-[10px] font-semibold uppercase tracking-wider opacity-80">{{ $label }}</p>
                                        <p class="text-lg font-bold mt-0.5">{{ $value }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-500 mt-2">
                                Total working days = {{ $b['total_days'] }} days − {{ $b['week_off_days'] }} week offs − {{ $b['holiday_days'] }} holidays = <span class="font-semibold text-slate-700">{{ $b['working_days'] }}</span>.
                                Present days count only working days (work on holidays/week offs is not counted).
                                @if($b['upcoming_days'] > 0)
                                    <span class="text-amber-700 font-medium">{{ $b['upcoming_days'] }} working day(s) of this month are still upcoming and are not payable yet.</span>
                                @endif
                            </p>
                        </div>

                        <!-- Leaves & Holidays -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-md border border-slate-200">
                                <div class="px-3 py-2 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700">Leave Days</span>
                                    <span class="text-[11px] text-slate-500">
                                        Paid: <span class="font-semibold text-emerald-700">{{ $days($b['paid_leave_days']) }}</span>
                                        · Unpaid: <span class="font-semibold text-rose-700">{{ $days($b['unpaid_leave_days']) }}</span>
                                    </span>
                                </div>
                                <ul class="divide-y divide-slate-100 max-h-40 overflow-y-auto text-xs">
                                    @forelse($b['leaves'] as $leave)
                                        <li class="px-3 py-1.5 flex items-center justify-between gap-2">
                                            <span class="text-slate-700">{{ \Carbon\Carbon::parse($leave['date'])->format('D, M d') }}</span>
                                            <span class="flex items-center gap-1.5">
                                                <span class="capitalize text-slate-500">{{ str_replace('_', ' ', $leave['category']) }}{{ $leave['days'] < 1 ? ' (½)' : '' }}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $leave['paid'] ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                                    {{ $leave['paid'] ? 'PAID' : (($leave['over_limit'] ?? false) ? 'UNPAID · OVER LIMIT' : 'UNPAID') }}
                                                </span>
                                            </span>
                                        </li>
                                    @empty
                                        <li class="px-3 py-3 text-slate-400 text-center">No leaves this month</li>
                                    @endforelse
                                </ul>
                                <p class="px-3 py-1.5 border-t border-slate-200 text-[11px] text-slate-500">
                                    Only {{ $days($b['paid_leave_limit'] ?? 2) }} paid leave days per month are payable.
                                    @if(($b['excess_leave_days'] ?? 0) > 0)
                                        <span class="text-rose-700 font-medium">{{ $days($b['excess_leave_days']) }} day(s) over the limit counted as unpaid.</span>
                                    @endif
                                </p>
                            </div>

                            <div class="rounded-md border border-slate-200">
                                <div class="px-3 py-2 border-b border-slate-200 bg-slate-50">
                                    <span class="text-xs font-bold text-slate-700">Holidays (not counted as working days)</span>
                                </div>
                                <ul class="divide-y divide-slate-100 max-h-40 overflow-y-auto text-xs">
                                    @forelse($b['holidays'] as $holiday)
                                        <li class="px-3 py-1.5 flex items-center justify-between gap-2">
                                            <span class="text-slate-700">{{ \Carbon\Carbon::parse($holiday['date'])->format('D, M d') }}</span>
                                            <span class="text-purple-700 font-medium truncate">{{ $holiday['name'] }}</span>
                                        </li>
                                    @empty
                                        <li class="px-3 py-3 text-slate-400 text-center">No holidays this month</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Salary calculation -->
                        <div class="rounded-md border border-slate-200 overflow-hidden">
                            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">Salary Calculation</div>
                            <dl class="divide-y divide-slate-100 text-sm">
                                <div class="px-3 py-2 flex justify-between">
                                    <dt class="text-slate-500">Monthly Salary</dt>
                                    <dd class="font-semibold text-slate-900">{{ $money($b['monthly_salary']) }}</dd>
                                </div>
                                <div class="px-3 py-2 flex justify-between">
                                    <dt class="text-slate-500">Per Day Salary <span class="text-[11px]">({{ $money($b['monthly_salary']) }} ÷ {{ $b['working_days'] }} working days)</span></dt>
                                    <dd class="font-semibold text-slate-900">{{ $money($b['per_day_salary']) }}</dd>
                                </div>
                                <div class="px-3 py-2 flex justify-between">
                                    <dt class="text-slate-500">Payable Days <span class="text-[11px]">({{ $days($b['present_days']) }} present + {{ $days($b['paid_leave_days']) }} paid leave)</span></dt>
                                    <dd class="font-semibold text-slate-900">{{ $days($b['payable_days']) }}</dd>
                                </div>
                                <div class="px-3 py-2 flex justify-between bg-slate-50/60">
                                    <dt class="text-slate-700 font-semibold">Calculated Salary</dt>
                                    <dd class="font-bold text-slate-900">{{ $money($b['calculated_salary']) }}</dd>
                                </div>
                                @if($selectedPayment && ! $editingPayment)
                                    <div class="px-3 py-2 flex justify-between">
                                        <dt class="text-slate-500">Bonus</dt>
                                        <dd class="font-semibold text-emerald-700">+ {{ $money($selectedPayment->bonus) }}</dd>
                                    </div>
                                    @if((float) $selectedPayment->deduction > 0)
                                        <div class="px-3 py-2 flex justify-between">
                                            <dt class="text-slate-500">Deduction</dt>
                                            <dd class="font-semibold text-rose-700">− {{ $money($selectedPayment->deduction) }}</dd>
                                        </div>
                                    @endif
                                    <div class="px-3 py-2.5 flex justify-between bg-emerald-50">
                                        <dt class="text-emerald-800 font-bold">Net Salary Paid</dt>
                                        <dd class="font-bold text-emerald-800 text-base">{{ $money($selectedPayment->net_salary) }}</dd>
                                    </div>
                                    @if((float) $selectedPayment->calculated_salary !== (float) $b['calculated_salary'])
                                        <div class="px-3 py-2 text-[11px] text-amber-700 bg-amber-50">
                                            Attendance changed after payment. Amount calculated at payment time was {{ $money($selectedPayment->calculated_salary) }}.
                                        </div>
                                    @endif
                                    @if($selectedPayment->notes)
                                        <div class="px-3 py-2 text-xs text-slate-600"><span class="font-semibold">Notes:</span> {{ $selectedPayment->notes }}</div>
                                    @endif
                                @endif
                            </dl>
                        </div>

                        <!-- Payment form (new payment, or editing an existing one) -->
                        @if(! $selectedPayment || $editingPayment)
                            <div
                                class="rounded-md border {{ $editingPayment ? 'border-blue-200' : 'border-slate-200' }} p-4 space-y-4"
                                x-data="{
                                    live: {{ (float) $b['calculated_salary'] }},
                                    stored: {{ $selectedPayment ? (float) $selectedPayment->calculated_salary : (float) $b['calculated_salary'] }},
                                    get base() { return {{ $editingPayment ? 'true' : 'false' }} && ! this.$wire.recalculate ? this.stored : this.live; }
                                }"
                            >
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $editingPayment ? 'Edit Salary Payment' : 'Pay Salary' }}</h4>
                                @if($editingPayment)
                                    <label class="flex items-start gap-2 rounded-md bg-slate-50 border border-slate-200 px-3 py-2 text-xs text-slate-700 cursor-pointer">
                                        <input type="checkbox" wire:model.live="recalculate" class="mt-0.5 rounded border-slate-300" />
                                        <span>
                                            <span class="font-semibold">Recalculate from current attendance</span>
                                            <span class="block text-slate-500">
                                                Calculated salary at payment time: {{ $money($selectedPayment->calculated_salary) }} · Current: {{ $money($b['calculated_salary']) }}
                                            </span>
                                        </span>
                                    </label>
                                @endif
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Bonus / Incentive (₹)</label>
                                        <input type="number" step="0.01" min="0" wire:model="bonus" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900" />
                                        @error('bonus') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                                        <textarea wire:model="notes" rows="2" placeholder="Transaction ID, remarks..." class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"></textarea>
                                        @error('notes') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="flex items-center justify-between rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3">
                                    <span class="text-sm font-bold text-emerald-800">{{ $editingPayment ? 'Updated Net Salary' : 'Net Salary to Pay' }}</span>
                                    <span
                                        class="text-lg font-bold text-emerald-800"
                                        x-text="'₹' + Math.max(0, base + (parseFloat($wire.bonus) || 0) - (parseFloat($wire.deduction) || 0)).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"
                                    ></span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button
                            type="button"
                            wire:click="closeSalaryModal"
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded-md border border-slate-300 cursor-pointer"
                        >
                            Close
                        </button>
                        @if($selectedPayment && ! $editingPayment)
                            <a
                                href="{{ route('salary-slips.download', $selectedPayment) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded-md border border-slate-300"
                            >
                                <i class="ri-download-2-line"></i>
                                Download Slip
                            </a>
                            <button
                                type="button"
                                wire:click="startEditPayment"
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md cursor-pointer shadow-sm"
                            >
                                <i class="ri-edit-line"></i>
                                Edit Payment
                            </button>
                        @elseif($selectedPayment && $editingPayment)
                            <button
                                type="button"
                                wire:click="cancelEditPayment"
                                class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded-md border border-slate-300 cursor-pointer"
                            >
                                Cancel Edit
                            </button>
                            <button
                                type="button"
                                wire:click="updatePayment"
                                wire:confirm="Save changes to this salary payment?"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md cursor-pointer shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i wire:loading.remove wire:target="updatePayment" class="ri-save-line"></i>
                                <i wire:loading wire:target="updatePayment" class="ri-loader-4-line animate-spin"></i>
                                Save Changes
                            </button>
                        @endif
                        @unless($selectedPayment)
                            <button
                                type="button"
                                wire:click="paySalary"
                                wire:confirm="Confirm salary payment for {{ $selectedEmployee->name }} ({{ $b['month_label'] }})?"
                                wire:loading.attr="disabled"
                                @disabled((float) $b['monthly_salary'] <= 0)
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md cursor-pointer shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i wire:loading.remove wire:target="paySalary" class="ri-secure-payment-line"></i>
                                <i wire:loading wire:target="paySalary" class="ri-loader-4-line animate-spin"></i>
                                Pay Salary
                            </button>
                        @endunless
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
