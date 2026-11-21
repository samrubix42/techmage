@php
    $money = fn ($v) => '₹'.number_format((float) $v, 2);
    $days = fn ($v) => rtrim(rtrim(number_format((float) $v, 1), '0'), '.');
@endphp

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="ri-wallet-3-line text-slate-500"></i>
                        Payroll
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">My Salary Slips</h1>
                <p class="text-slate-500 mt-1 text-xs sm:text-sm">View your monthly salary breakdown and download official salary slips as PDF.</p>
            </div>

            @if($latest)
                <a
                    href="{{ route('salary-slips.download', $latest) }}"
                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-xs inline-flex items-center gap-2"
                >
                    <i class="ri-download-2-line text-base"></i>
                    <span>Latest Slip ({{ $latest->salary_month->format('M Y') }})</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-md p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Monthly Salary</span>
                <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center"><i class="ri-money-rupee-circle-line text-base"></i></div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $money($monthlySalary) }}</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Current fixed salary</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-md p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Last Net Pay</span>
                <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="ri-hand-coin-line text-base"></i></div>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ $latest ? $money($latest->net_salary) : '—' }}</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">{{ $latest ? $latest->salary_month->format('F Y') : 'No salary paid yet' }}</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-md p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Earned in {{ $currentYear }}</span>
                <div class="w-8 h-8 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center"><i class="ri-line-chart-line text-base"></i></div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $money($yearEarnings) }}</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Total net salary this year</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-md p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Salary Slips</span>
                <div class="w-8 h-8 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center"><i class="ri-file-list-3-line text-base"></i></div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalSlips }}</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Available to download</p>
        </div>
    </div>

    <!-- List -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-md bg-slate-50/80 border border-slate-200/90">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Year:</span>
                <select wire:model.live="yearFilter" class="py-1.5 px-3 text-xs rounded-md border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 bg-white font-medium text-slate-800">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-[11px] text-slate-500">Paid leave is not deducted. Only unpaid leave and absent days are deducted as loss of pay.</p>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-md">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="uppercase bg-slate-50/80 text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Pay Period</th>
                        <th class="px-4 py-3">Payable Days</th>
                        <th class="px-4 py-3">Gross Salary</th>
                        <th class="px-4 py-3">Bonus</th>
                        <th class="px-4 py-3">Deductions</th>
                        <th class="px-4 py-3">Net Pay</th>
                        <th class="px-4 py-3">Paid On</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($slips as $slip)
                        @php $lop = max(0, (float) $slip->monthly_salary - (float) $slip->calculated_salary) + (float) $slip->deduction; @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors" wire:key="slip-{{ $slip->id }}">
                            <td class="px-4 py-3.5">
                                <p class="font-semibold text-slate-900">{{ $slip->salary_month->format('F Y') }}</p>
                                <p class="text-[11px] text-slate-500">{{ $slip->working_days }} working days</p>
                            </td>
                            <td class="px-4 py-3.5 font-medium">{{ $days($slip->payable_days) }} / {{ $slip->working_days }}</td>
                            <td class="px-4 py-3.5">{{ $money($slip->monthly_salary) }}</td>
                            <td class="px-4 py-3.5 text-emerald-700">{{ (float) $slip->bonus > 0 ? '+ '.$money($slip->bonus) : '—' }}</td>
                            <td class="px-4 py-3.5 text-rose-700">{{ $lop > 0 ? '− '.$money($lop) : '—' }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">{{ $money($slip->net_salary) }}</td>
                            <td class="px-4 py-3.5">
                                <p>{{ $slip->payment_date?->format('M d, Y') }}</p>
                                <p class="text-[11px] text-slate-500">Bank Transfer</p>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <button
                                    wire:click="viewSlip({{ $slip->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 cursor-pointer"
                                >
                                    <i class="ri-eye-line"></i> View
                                </button>
                                <a
                                    href="{{ route('salary-slips.download', $slip) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800"
                                >
                                    <i class="ri-download-2-line"></i> PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <i class="ri-file-list-3-line text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-500 mt-2">No salary slips yet. Slips appear here once your salary is paid.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $slips->links() }}</div>
    </div>

    <!-- Slip detail modal -->
    @if($selected)
        @php
            $lossOfPay = max(0, (float) $selected->monthly_salary - (float) $selected->calculated_salary);
            $lopDays = max(0, (float) $selected->working_days - (float) $selected->payable_days);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50" wire:click.self="closeSlip" @keydown.escape.window="$wire.closeSlip()">
            <div class="w-full max-w-2xl bg-white rounded-lg border border-slate-200 shadow-xl max-h-[92vh] flex flex-col">
                <div class="flex items-start justify-between px-6 py-4 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Salary Slip · {{ $selected->salary_month->format('F Y') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Paid on {{ $selected->payment_date?->format('M d, Y') }} via Bank Transfer</p>
                    </div>
                    <button wire:click="closeSlip" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer"><i class="ri-close-line text-xl"></i></button>
                </div>

                <div class="p-6 space-y-5 overflow-y-auto">
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 text-center">
                        @foreach([
                            ['Working', $selected->working_days],
                            ['Present', $days($selected->present_days)],
                            ['Paid Leave', $days($selected->paid_leave_days)],
                            ['Unpaid Leave', $days($selected->unpaid_leave_days)],
                            ['Absent', $days($selected->absent_days)],
                            ['Payable', $days($selected->payable_days)],
                        ] as [$label, $value])
                            <div class="rounded-md border border-slate-200 bg-slate-50 px-2 py-2">
                                <p class="text-base font-bold text-slate-900">{{ $value }}</p>
                                <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-md border border-slate-200 overflow-hidden">
                            <div class="px-3 py-2 bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider">Earnings</div>
                            <dl class="divide-y divide-slate-100 text-sm">
                                <div class="px-3 py-2 flex justify-between"><dt class="text-slate-600">Monthly Salary</dt><dd class="font-semibold">{{ $money($selected->monthly_salary) }}</dd></div>
                                @if((float) $selected->bonus > 0)
                                    <div class="px-3 py-2 flex justify-between"><dt class="text-slate-600">Bonus / Incentive</dt><dd class="font-semibold text-emerald-700">{{ $money($selected->bonus) }}</dd></div>
                                @endif
                                <div class="px-3 py-2 flex justify-between bg-slate-50 font-bold"><dt>Total</dt><dd>{{ $money((float) $selected->monthly_salary + (float) $selected->bonus) }}</dd></div>
                            </dl>
                        </div>
                        <div class="rounded-md border border-slate-200 overflow-hidden">
                            <div class="px-3 py-2 bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider">Deductions</div>
                            <dl class="divide-y divide-slate-100 text-sm">
                                <div class="px-3 py-2 flex justify-between"><dt class="text-slate-600">Loss of Pay ({{ $days($lopDays) }} days)</dt><dd class="font-semibold text-rose-700">{{ $money($lossOfPay) }}</dd></div>
                                @if((float) $selected->deduction > 0)
                                    <div class="px-3 py-2 flex justify-between"><dt class="text-slate-600">Other Deduction</dt><dd class="font-semibold text-rose-700">{{ $money($selected->deduction) }}</dd></div>
                                @endif
                                <div class="px-3 py-2 flex justify-between bg-slate-50 font-bold"><dt>Total</dt><dd>{{ $money($lossOfPay + (float) $selected->deduction) }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3">
                        <span class="text-sm font-bold text-emerald-800">Net Salary Paid</span>
                        <span class="text-xl font-bold text-emerald-800">{{ $money($selected->net_salary) }}</span>
                    </div>
                    <p class="text-xs text-slate-600 -mt-2">{{ \App\Support\AmountInWords::rupees((float) $selected->net_salary) }}</p>

                    @if($selected->notes)
                        <p class="text-xs text-slate-600 border-l-2 border-slate-300 pl-3"><span class="font-semibold">Remarks:</span> {{ $selected->notes }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <a href="{{ route('salary-slips.download', ['payment' => $selected, 'preview' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-xs font-medium text-slate-700 bg-white hover:bg-slate-100 border border-slate-300">
                        <i class="ri-external-link-line"></i> Open PDF
                    </a>
                    <a href="{{ route('salary-slips.download', $selected) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800">
                        <i class="ri-download-2-line"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
