<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;

/**
 * Calculates an employee's salary for one month.
 *
 * Rules:
 *  - Week offs (Sunday + employee's Saturday policy) and holidays are NOT working days.
 *  - Total working days = days in month - week offs - holidays.
 *  - Present days only count attendance on working days (work done on a holiday/week off is ignored).
 *  - A half day counts as 0.5 present; the other half is leave (if approved) or absent.
 *  - Paid leave is paid only up to PAID_LEAVE_LIMIT days per month (in date order);
 *    any paid leave beyond that becomes unpaid. Unpaid leave and absent days are not paid.
 *  - Per day salary = monthly salary / total working days.
 *  - Calculated salary = per day salary * (present days + paid leave days).
 */
class SalaryCalculator
{
    /** Maximum number of leave days per month that are paid. */
    public const PAID_LEAVE_LIMIT = 2.0;

    /** Paid leave days already used in the month currently being calculated. */
    private float $paidLeaveUsed = 0.0;

    public function calculate(User $user, int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->startOfDay();
        $today = now()->startOfDay();

        $holidays = Holiday::forMonth($year, $month)->get()
            ->keyBy(fn ($h) => $h->holiday_date->toDateString());

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($a) => $a->attendance_date->toDateString());

        $totalDays = $start->daysInMonth;
        $weekOffDays = 0;
        $holidayDays = 0;
        $workingDays = 0;
        $presentDays = 0.0;
        $paidLeaveDays = 0.0;
        $unpaidLeaveDays = 0.0;
        $absentDays = 0.0;
        $upcomingDays = 0;
        $excessLeaveDays = 0.0;
        $this->paidLeaveUsed = 0.0;

        $holidayList = [];
        $leaveList = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();

            if ($user->isOffDay($date)) {
                $weekOffDays++;

                continue;
            }

            if ($holidays->has($key)) {
                $holidayDays++;
                $holidayList[] = ['date' => $key, 'name' => $holidays[$key]->name];

                continue;
            }

            $workingDays++;
            $attendance = $attendances->get($key);

            if (! $attendance) {
                if ($date->gt($today)) {
                    $upcomingDays++;
                } else {
                    $absentDays++;
                }

                continue;
            }

            $isPaidLeave = ($attendance->leave_payment_type ?? 'paid') !== 'unpaid';

            switch ($attendance->status) {
                case 'present':
                    $presentDays += 1;
                    break;

                case 'half_day':
                    $presentDays += 0.5;
                    if ($attendance->leave_request_id) {
                        $this->addLeave($key, $attendance, 0.5, $isPaidLeave, $paidLeaveDays, $unpaidLeaveDays, $excessLeaveDays, $leaveList);
                    } else {
                        $absentDays += 0.5;
                    }
                    break;

                case 'on_leave':
                case 'holiday': // leave approved under the "holiday" category
                    $this->addLeave($key, $attendance, 1.0, $isPaidLeave, $paidLeaveDays, $unpaidLeaveDays, $excessLeaveDays, $leaveList);
                    break;

                default: // absent / anything else
                    if ($date->gt($today)) {
                        $upcomingDays++;
                    } else {
                        $absentDays++;
                    }
                    break;
            }
        }

        $monthlySalary = (float) $user->salary;
        $perDaySalary = $workingDays > 0 ? $monthlySalary / $workingDays : 0;
        $payableDays = $presentDays + $paidLeaveDays;
        $calculatedSalary = round($perDaySalary * $payableDays, 2);

        return [
            'month_label' => $start->format('F Y'),
            'salary_month' => $start->toDateString(),
            'monthly_salary' => round($monthlySalary, 2),
            'total_days' => $totalDays,
            'week_off_days' => $weekOffDays,
            'holiday_days' => $holidayDays,
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'paid_leave_days' => $paidLeaveDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'leave_days' => $paidLeaveDays + $unpaidLeaveDays,
            'paid_leave_limit' => self::PAID_LEAVE_LIMIT,
            'excess_leave_days' => $excessLeaveDays,
            'absent_days' => $absentDays,
            'upcoming_days' => $upcomingDays,
            'payable_days' => $payableDays,
            'per_day_salary' => round($perDaySalary, 2),
            'calculated_salary' => $calculatedSalary,
            'holidays' => $holidayList,
            'leaves' => $leaveList,
        ];
    }

    /**
     * Record a leave, paying it only while the monthly paid-leave limit has room.
     * A leave that crosses the limit is split into a paid and an unpaid part.
     */
    private function addLeave(
        string $date,
        Attendance $attendance,
        float $days,
        bool $isPaidLeave,
        float &$paidLeaveDays,
        float &$unpaidLeaveDays,
        float &$excessLeaveDays,
        array &$leaveList,
    ): void {
        if (! $isPaidLeave) {
            $unpaidLeaveDays += $days;
            $leaveList[] = $this->leaveRow($date, $attendance, $days, false);

            return;
        }

        $paidPart = min($days, max(0.0, self::PAID_LEAVE_LIMIT - $this->paidLeaveUsed));
        $unpaidPart = $days - $paidPart;

        if ($paidPart > 0) {
            $this->paidLeaveUsed += $paidPart;
            $paidLeaveDays += $paidPart;
            $leaveList[] = $this->leaveRow($date, $attendance, $paidPart, true);
        }

        if ($unpaidPart > 0) {
            $unpaidLeaveDays += $unpaidPart;
            $excessLeaveDays += $unpaidPart;
            $leaveList[] = $this->leaveRow($date, $attendance, $unpaidPart, false, true);
        }
    }

    private function leaveRow(string $date, Attendance $attendance, float $days, bool $paid, bool $overLimit = false): array
    {
        return [
            'date' => $date,
            'category' => $attendance->leave_category ?: 'leave',
            'days' => $days,
            'paid' => $paid,
            'over_limit' => $overLimit,
        ];
    }
}
