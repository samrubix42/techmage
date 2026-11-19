@php
    $money = fn ($v) => '₹ '.number_format((float) $v, 2);
    $days = fn ($v) => \App\Http\Controllers\SalarySlipController::days((float) $v);
    $rows = max(count($earnings), count($deductions));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $employee?->name }} - {{ $payment->salary_month->format('F Y') }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }

        .header td { vertical-align: middle; }
        .company-name { font-size: 17px; font-weight: bold; color: #0f172a; }
        .company-meta { font-size: 8.5px; color: #64748b; line-height: 1.5; }
        .slip-title { font-size: 15px; font-weight: bold; color: #0f172a; letter-spacing: 1.5px; }
        .slip-sub { font-size: 9.5px; color: #475569; margin-top: 3px; }
        .rule { height: 3px; background: #0f172a; margin: 12px 0 14px; }

        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; background: #f1f5f9; padding: 6px 8px; border: 1px solid #cbd5e1; border-bottom: none; }

        .details { border: 1px solid #cbd5e1; }
        .details td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; width: 25%; }
        .details tr:last-child td { border-bottom: none; }
        .details .label { color: #64748b; }
        .details .value { font-weight: bold; color: #0f172a; }

        .attendance { border: 1px solid #cbd5e1; }
        .attendance td { width: 16.66%; text-align: center; padding: 7px 4px; border-right: 1px solid #e2e8f0; }
        .attendance td:last-child { border-right: none; }
        .attendance .num { font-size: 13px; font-weight: bold; color: #0f172a; }
        .attendance .cap { font-size: 7.5px; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-top: 2px; }

        .pay { border: 1px solid #cbd5e1; }
        .pay th { background: #0f172a; color: #fff; font-size: 9px; text-transform: uppercase; letter-spacing: .8px; padding: 7px 8px; text-align: left; }
        .pay th.right { text-align: right; }
        .pay td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; }
        .pay td.split { border-right: 1px solid #cbd5e1; }
        .pay tr.total td { background: #f8fafc; font-weight: bold; border-top: 1px solid #cbd5e1; border-bottom: none; }

        .net { border: 1px solid #0f172a; margin-top: 14px; }
        .net td { padding: 10px 12px; }
        .net .net-label { background: #0f172a; color: #fff; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; width: 38%; }
        .net .net-amount { font-size: 17px; font-weight: bold; color: #0f172a; text-align: right; }
        .words { margin-top: 6px; font-size: 9.5px; }

        .note { margin-top: 12px; padding: 7px 9px; border-left: 3px solid #94a3b8; background: #f8fafc; font-size: 9px; }
        .policy { margin-top: 8px; font-size: 8.5px; color: #64748b; }

        .footer { position: fixed; bottom: -8px; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="footer">
        This is a computer-generated salary slip. &nbsp;·&nbsp; Generated on {{ now()->format('d M Y, h:i A') }}
    </div>

    <!-- Header -->
    <table class="header">
        <tr>
            <td style="width: 60%;">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $company['name'] }}" style="height: 38px; margin-bottom: 5px;">
                @endif
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-meta">
                    @if($company['address']) {{ $company['address'] }}<br> @endif
                    @php $contact = array_filter([$company['email'], $company['phone'], $company['website']]); @endphp
                    @if($contact) {{ implode('  |  ', $contact) }} @endif
                </div>
            </td>
            <td style="width: 40%;" class="right">
                <div class="slip-title">SALARY SLIP</div>
                <div class="slip-sub">Pay Period: <span class="bold">{{ $payment->salary_month->format('F Y') }}</span></div>
                <div class="slip-sub">Slip No: <span class="bold">{{ $slipNumber }}</span></div>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    <!-- Employee details -->
    <div class="section-title">Employee Details</div>
    <table class="details">
        <tr>
            <td class="label">Employee Name</td>
            <td class="value">{{ trim($employee?->name ?? '—') }}</td>
            <td class="label">Employee ID</td>
            <td class="value">{{ $employeeCode }}</td>
        </tr>
        <tr>
            <td class="label">Department</td>
            <td class="value">{{ $employee?->department?->name ?? '—' }}</td>
            <td class="label">Email</td>
            <td class="value">{{ $employee?->email ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Payment Date</td>
            <td class="value">{{ $payment->payment_date?->format('d M Y') }}</td>
            <td class="label">Payment Mode</td>
            <td class="value">Bank Transfer</td>
        </tr>
        <tr>
            <td class="label">Monthly Salary</td>
            <td class="value">{{ $money($payment->monthly_salary) }}</td>
            <td class="label">Per Day Salary</td>
            <td class="value">{{ $money($payment->per_day_salary) }}</td>
        </tr>
    </table>

    <!-- Attendance summary -->
    <div class="section-title" style="margin-top: 14px;">Attendance Summary</div>
    <table class="attendance">
        <tr>
            <td><div class="num">{{ $payment->total_days }}</div><div class="cap">Calendar Days</div></td>
            <td><div class="num">{{ $payment->working_days }}</div><div class="cap">Working Days</div></td>
            <td><div class="num">{{ $days($payment->present_days) }}</div><div class="cap">Present</div></td>
            <td><div class="num">{{ $days($payment->paid_leave_days) }}</div><div class="cap">Paid Leave</div></td>
            <td><div class="num">{{ $days($lopDays) }}</div><div class="cap">Loss of Pay Days</div></td>
            <td><div class="num">{{ $days($payment->payable_days) }}</div><div class="cap">Payable Days</div></td>
        </tr>
    </table>
    <div class="policy">
        Week offs: {{ $payment->week_off_days }} &nbsp;·&nbsp; Holidays: {{ $payment->holiday_days }}
        &nbsp;·&nbsp; Unpaid leave: {{ $days($payment->unpaid_leave_days) }} &nbsp;·&nbsp; Absent: {{ $days($payment->absent_days) }}
        &nbsp;·&nbsp; Only {{ $days(\App\Services\SalaryCalculator::PAID_LEAVE_LIMIT) }} paid leave days are allowed per month.
    </div>

    <!-- Earnings & deductions -->
    <table class="pay" style="margin-top: 14px;">
        <thead>
            <tr>
                <th style="width: 32%;">Earnings</th>
                <th class="right" style="width: 18%;">Amount</th>
                <th style="width: 32%;">Deductions</th>
                <th class="right" style="width: 18%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < $rows; $i++)
                <tr>
                    <td>{{ $earnings[$i]['label'] ?? '' }}</td>
                    <td class="right split">{{ isset($earnings[$i]) ? $money($earnings[$i]['amount']) : '' }}</td>
                    <td>{{ $deductions[$i]['label'] ?? '' }}</td>
                    <td class="right">{{ isset($deductions[$i]) ? $money($deductions[$i]['amount']) : '' }}</td>
                </tr>
            @endfor
            <tr class="total">
                <td>Total Earnings</td>
                <td class="right split">{{ $money($totalEarnings) }}</td>
                <td>Total Deductions</td>
                <td class="right">{{ $money($totalDeductions) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Net pay -->
    <table class="net">
        <tr>
            <td class="net-label">Net Salary Paid</td>
            <td class="net-amount">{{ $money($payment->net_salary) }}</td>
        </tr>
    </table>
    <div class="words"><span class="muted">Amount in words:</span> <span class="bold">{{ $netInWords }}</span></div>

    @if($payment->notes)
        <div class="note"><span class="bold">Remarks:</span> {{ $payment->notes }}</div>
    @endif
</body>
</html>
