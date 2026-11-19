<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\DailySlotTracking;
use App\Models\DailyTaskReport;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Services\SalaryCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Fills the database with fake demo data so every screen has something to show.
 *
 * Run with: php artisan db:seed --class=DemoDataSeeder
 *
 * - Creates demo employees (emails end with @demo.test, password: "password").
 *   Re-running deletes and recreates them, so it is safe to run many times.
 * - Attendance + clock logs + slot tracking from START_DATE until today
 *   (some employees are clocked in "right now").
 * - Leave requests: paid, unpaid, half day, one employee over the
 *   2 paid leave/month limit, plus pending and rejected requests.
 * - Daily task reports (some checked by admin).
 * - Salary payments for past months (latest month left partly unpaid).
 * - Gives existing employees without a salary a random salary.
 */
class DemoDataSeeder extends Seeder
{
    public const START_DATE = '2026-09-01';

    public const EMAIL_DOMAIN = 'demo.test';

    private array $employees = [
        ['Aarav Mehta', 'Engineering & Technology', 45000, 'sunday_2nd_4th_saturday', 'regular'],
        ['Priya Verma', 'Human Resources', 32000, 'sunday_only', 'regular'],
        ['Rohan Gupta', 'Marketing & Sales', 28000, 'sunday_2nd_4th_saturday', 'leave_heavy'],
        ['Neha Singh', 'Product Design', 38000, 'sunday_2nd_4th_saturday', 'regular'],
        ['Vikram Rao', 'Customer Operations', 22000, 'sunday_only', 'absent_prone'],
        ['Ananya Iyer', 'Finance & Accounting', 52000, 'sunday_2nd_4th_saturday', 'regular'],
        ['Karan Malhotra', 'Engineering & Technology', 60000, 'sunday_2nd_4th_saturday', 'late_comer'],
        ['Sneha Kapoor', 'Marketing & Sales', 26000, 'sunday_only', 'regular'],
    ];

    private array $tasks = [
        ['Client Website', 'Fixed responsive issues on the landing page and updated the hero section.'],
        ['Social Media', 'Scheduled posts for Instagram and Facebook for the coming week.'],
        ['HR Portal', 'Built the attendance export feature and tested it with sample data.'],
        ['SEO Audit', 'Completed on-page SEO audit and shared the report with the client.'],
        ['Payroll', 'Verified attendance records and prepared the salary sheet.'],
        ['Design Review', 'Created two new banner concepts and collected feedback.'],
        ['Support Tickets', 'Resolved 12 customer tickets and escalated 2 billing issues.'],
        ['Team Meeting', 'Weekly sprint planning and task allocation.'],
        ['Bug Fixes', 'Fixed login redirect bug and a date formatting issue in reports.'],
        ['Content Writing', 'Wrote two blog articles and a product description page.'],
        ['Google Ads', 'Optimised campaign keywords and reduced cost per click.'],
        ['Invoices', 'Generated client invoices and followed up on pending payments.'],
    ];

    public function run(): void
    {
        mt_srand(2026);
        fake()->seed(2026);

        Model::unguarded(function () {
            $this->resetDemoData();

            $admin = User::where('role', 'admin')->orderBy('id')->first();
            $holidays = Holiday::pluck('holiday_date')->map(fn ($d) => Carbon::parse($d)->toDateString())->flip();

            $created = [];
            foreach ($this->employees as $i => [$name, $deptName, $salary, $policy, $profile]) {
                $user = $this->createEmployee($name, $deptName, $salary, $policy);
                $this->seedLeaves($user, $profile, $i, $admin);
                $this->seedAttendance($user, $profile, $holidays, $admin);
                $created[] = $user;
            }

            // The demo employee login (employee@employee.com) also gets activity
            if ($demo = User::where('email', 'employee@employee.com')->first()) {
                $this->clearUserActivity($demo);
                $demo->update([
                    'salary' => 30000,
                    'saturday_off_policy' => 'sunday_2nd_4th_saturday',
                    'department_id' => Department::where('slug', 'engineering-technology')->value('id') ?? $demo->department_id,
                ]);
                $this->seedLeaves($demo, 'regular', count($this->employees), $admin);
                $this->seedAttendance($demo, 'regular', $holidays, $admin);
                $created[] = $demo;
            }

            $this->giveSalaryToExistingEmployees();
            $this->seedSalaryPayments($created, $admin);
        });

        $this->command?->info('Demo data seeded: '.count($this->employees).' employees (login: <name>@'.self::EMAIL_DOMAIN.' / password).');
    }

    private function resetDemoData(): void
    {
        // Cascades remove their attendance, logs, slots, leaves, reports and payments
        User::where('email', 'like', '%@'.self::EMAIL_DOMAIN)->get()->each->delete();
    }

    private function clearUserActivity(User $user): void
    {
        SalaryPayment::where('user_id', $user->id)->delete();
        DailyTaskReport::where('user_id', $user->id)->delete();
        DailySlotTracking::where('user_id', $user->id)->delete();
        AttendanceLog::where('user_id', $user->id)->delete();
        Attendance::where('user_id', $user->id)->delete();
        LeaveRequest::where('user_id', $user->id)->delete();
    }

    private function createEmployee(string $name, string $deptName, int $salary, string $policy): User
    {
        $department = Department::firstOrCreate(
            ['slug' => Str::slug($deptName)],
            ['name' => $deptName, 'is_active' => true],
        );

        return User::create([
            'name' => $name,
            'email' => Str::lower(Str::before($name, ' ')).'@'.self::EMAIL_DOMAIN,
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true,
            'department_id' => $department->id,
            'saturday_off_policy' => $policy,
            'salary' => $salary,
            'email_verified_at' => now(),
            'created_at' => Carbon::parse(self::START_DATE)->subMonth(),
        ]);
    }

    /**
     * Creates leave requests. Approved leave days are written to attendance later.
     */
    private function seedLeaves(User $user, string $profile, int $index, ?User $admin): void
    {
        $months = [[2026, 9], [2026, 10], [2026, 11]];

        foreach ($months as [$year, $month]) {
            $workDays = $this->workingDays($user, $year, $month, untilToday: true);
            if (count($workDays) < 6) {
                continue;
            }

            // How many leave days this month
            $count = match ($profile) {
                'leave_heavy' => 4,           // over the 2 paid leave limit
                'absent_prone' => mt_rand(1, 2),
                default => mt_rand(0, 2),
            };

            $picked = collect($workDays)->shuffle()->take($count)->sort()->values();

            foreach ($picked as $date) {
                $isHalf = $profile !== 'leave_heavy' && mt_rand(1, 5) === 1;
                $unpaid = $profile !== 'leave_heavy' && mt_rand(1, 6) === 1;
                $category = $isHalf ? 'half_day' : ($unpaid ? 'unpaid' : (mt_rand(0, 1) ? 'casual' : 'sick'));
                $paymentType = $category === 'unpaid' ? 'unpaid' : 'paid';

                $request = LeaveRequest::create([
                    'user_id' => $user->id,
                    'request_type' => $isHalf ? 'half_day' : 'single_day',
                    'start_date' => $date,
                    'end_date' => $date,
                    'total_days' => $isHalf ? 0.5 : 1,
                    'reason' => fake()->randomElement([
                        'Family function', 'Not feeling well', 'Doctor appointment',
                        'Personal work', 'Travelling to hometown', 'Fever and cold',
                    ]),
                    'status' => 'approved',
                    'leave_category' => $category,
                    'payment_type' => $paymentType,
                    'admin_remarks' => 'Approved',
                    'reviewed_by' => $admin?->id,
                    'reviewed_at' => Carbon::parse($date)->subDays(2)->setTime(11, 0),
                    'created_at' => Carbon::parse($date)->subDays(3)->setTime(10, 15),
                ]);

                Attendance::create([
                    'user_id' => $user->id,
                    'attendance_date' => $date,
                    'status' => $isHalf ? 'half_day' : 'on_leave',
                    'clock_in_time' => $isHalf ? Carbon::parse($date)->setTime(9, 35) : null,
                    'clock_out_time' => $isHalf ? Carbon::parse($date)->setTime(13, 40) : null,
                    'leave_request_id' => $request->id,
                    'leave_category' => $category,
                    'leave_payment_type' => $paymentType,
                ]);
            }
        }

        // Upcoming requests waiting for admin action, plus one rejected
        $future = now()->addDays(3 + $index)->startOfDay();
        while ($user->isOffDay($future)) {
            $future->addDay();
        }

        if ($index % 2 === 0) {
            LeaveRequest::create([
                'user_id' => $user->id,
                'request_type' => $index % 4 === 0 ? 'multiple_days' : 'single_day',
                'start_date' => $future->toDateString(),
                'end_date' => $index % 4 === 0 ? $future->copy()->addDay()->toDateString() : $future->toDateString(),
                'total_days' => $index % 4 === 0 ? 2 : 1,
                'reason' => 'Sister\'s wedding',
                'status' => 'pending',
                'leave_category' => 'casual',
                'payment_type' => 'paid',
            ]);
        }

        if ($index % 3 === 1) {
            LeaveRequest::create([
                'user_id' => $user->id,
                'request_type' => 'single_day',
                'start_date' => '2026-10-20',
                'end_date' => '2026-10-20',
                'total_days' => 1,
                'reason' => 'Want to attend a concert',
                'status' => 'rejected',
                'leave_category' => 'casual',
                'payment_type' => 'paid',
                'admin_remarks' => 'Project deadline that day, please reschedule.',
                'reviewed_by' => $admin?->id,
                'reviewed_at' => '2026-10-17 12:00:00',
            ]);
        }
    }

    private function seedAttendance(User $user, string $profile, $holidays, ?User $admin): void
    {
        $date = Carbon::parse(self::START_DATE);
        $today = now()->startOfDay();
        $existing = Attendance::where('user_id', $user->id)->pluck('attendance_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())->flip();

        for (; $date->lte($today); $date->addDay()) {
            $key = $date->toDateString();

            if ($user->isOffDay($date) || $holidays->has($key) || $existing->has($key)) {
                continue;
            }

            $isToday = $date->isSameDay($today);

            $absentChance = match ($profile) {
                'absent_prone' => 12,
                default => 3,
            };

            if (! $isToday && mt_rand(1, 100) <= $absentChance) {
                Attendance::create(['user_id' => $user->id, 'attendance_date' => $key, 'status' => 'absent']);

                continue;
            }

            // Some employees have not clocked in yet today
            if ($isToday && mt_rand(1, 4) === 1) {
                continue;
            }

            $lateBy = $profile === 'late_comer' ? mt_rand(10, 45) : mt_rand(-10, 12);
            $clockIn = $date->copy()->setTime(9, 30)->addMinutes($lateBy);

            // Today: still working (no clock out yet)
            $clockOut = $isToday ? null : $date->copy()->setTime(18, 30)->addMinutes(mt_rand(-20, 40));

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'attendance_date' => $key,
                'status' => 'present',
                'clock_in_time' => $clockIn,
                'clock_out_time' => $clockOut,
            ]);

            $this->seedLogs($attendance, $clockIn, $clockOut);
            $this->seedSlots($attendance, $clockIn, $isToday);

            if (! $isToday && mt_rand(1, 100) <= 85) {
                $this->seedTaskReport($user, $key, $clockOut, $admin);
            }
        }
    }

    private function seedLogs(Attendance $attendance, Carbon $clockIn, ?Carbon $clockOut): void
    {
        $lunchStart = $clockIn->copy()->setTime(13, 30)->addMinutes(mt_rand(-10, 10));
        $lunchEnd = $lunchStart->copy()->addMinutes(mt_rand(30, 50));

        if (! $clockOut && now()->lt($lunchStart)) {
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'clock_in_time' => $clockIn,
            ]);

            return;
        }

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'clock_in_time' => $clockIn,
            'clock_out_time' => $lunchStart,
            'duration_minutes' => $clockIn->diffInMinutes($lunchStart),
        ]);

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'clock_in_time' => $lunchEnd,
            'clock_out_time' => $clockOut,
            'duration_minutes' => $clockOut ? (int) $lunchEnd->diffInMinutes($clockOut) : null,
        ]);
    }

    private function seedSlots(Attendance $attendance, Carbon $clockIn, bool $isToday): void
    {
        $date = $clockIn->copy()->startOfDay();
        $slot2 = $date->copy()->setTime(11, 30)->addMinutes(mt_rand(-5, 20));
        $lunchStart = $date->copy()->setTime(13, 30)->addMinutes(mt_rand(-10, 10));
        $lunchMinutes = mt_rand(30, 55);
        $lunchEnd = $lunchStart->copy()->addMinutes($lunchMinutes);
        $slot4 = $date->copy()->setTime(16, 30)->addMinutes(mt_rand(-5, 25));
        $now = now();

        $past = fn (Carbon $t) => ! $isToday || $t->lte($now);
        $slot2Dev = (int) max(0, $slot2->diffInMinutes($date->copy()->setTime(11, 30), true) - 0);
        $slot4Dev = (int) $slot4->diffInMinutes($date->copy()->setTime(16, 30), true);

        DailySlotTracking::create([
            'user_id' => $attendance->user_id,
            'attendance_id' => $attendance->id,
            'tracking_date' => $date->toDateString(),
            'slot1_checkin_time' => $clockIn,
            'slot2_checkin_time' => $past($slot2) ? $slot2 : null,
            'slot2_timing_status' => $slot2Dev > 15 ? 'exceeded' : 'normal',
            'slot2_deviation_minutes' => $slot2Dev,
            'slot2_is_flagged' => $slot2Dev > 15,
            'lunch_start_time' => $past($lunchStart) ? $lunchStart : null,
            'lunch_end_time' => $past($lunchEnd) ? $lunchEnd : null,
            'lunch_duration_minutes' => $past($lunchEnd) ? $lunchMinutes : null,
            'lunch_exceeded' => $past($lunchEnd) && $lunchMinutes > 45,
            'lunch_exceeded_minutes' => $past($lunchEnd) ? max(0, $lunchMinutes - 45) : 0,
            'slot3_start_time' => $past($lunchEnd) ? $lunchEnd : null,
            'slot3_end_time' => $past($slot4) ? $slot4 : null,
            'slot4_checkin_time' => $past($slot4) ? $slot4 : null,
            'slot4_timing_status' => $slot4Dev > 15 ? 'exceeded' : 'normal',
            'slot4_deviation_minutes' => $slot4Dev,
            'slot4_is_flagged' => $slot4Dev > 15,
        ]);
    }

    private function seedTaskReport(User $user, string $date, ?Carbon $clockOut, ?User $admin): void
    {
        $items = collect($this->tasks)->shuffle()->take(mt_rand(2, 4))
            ->map(fn ($t) => ['title' => $t[0], 'description' => '<p>'.$t[1].'</p>'])
            ->values()->all();

        $checked = Carbon::parse($date)->lt(now()->subDays(3)) && mt_rand(1, 100) <= 70;

        DailyTaskReport::create([
            'user_id' => $user->id,
            'date' => $date,
            'title_description' => $items,
            'is_checked' => $checked,
            'checked_at' => $checked ? Carbon::parse($date)->addDay()->setTime(11, 0) : null,
            'checked_by' => $checked ? $admin?->id : null,
            'admin_notes' => $checked && mt_rand(1, 4) === 1 ? 'Good work, keep it up.' : null,
            'created_at' => $clockOut ?? Carbon::parse($date)->setTime(18, 0),
        ]);
    }

    private function giveSalaryToExistingEmployees(): void
    {
        User::where('role', 'employee')
            ->where(fn ($q) => $q->whereNull('salary')->orWhere('salary', '<=', 0))
            ->get()
            ->each(fn (User $u) => $u->update(['salary' => mt_rand(20, 50) * 1000]));
    }

    /**
     * Pays September for everyone and October for most, so the
     * payroll page shows both paid and pending employees.
     */
    private function seedSalaryPayments(array $users, ?User $admin): void
    {
        $calculator = app(SalaryCalculator::class);

        foreach ($users as $i => $user) {
            foreach ([[2026, 9], [2026, 10]] as [$year, $month]) {
                if ($month === 10 && $i % 3 === 2) {
                    continue; // left pending
                }

                $calc = $calculator->calculate($user, $year, $month);
                $bonus = mt_rand(1, 4) === 1 ? mt_rand(5, 20) * 100 : 0;
                $deduction = mt_rand(1, 6) === 1 ? mt_rand(2, 10) * 100 : 0;

                SalaryPayment::create([
                    'user_id' => $user->id,
                    'salary_month' => Carbon::create($year, $month, 1)->toDateString(),
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
                    'net_salary' => max(0, round($calc['calculated_salary'] + $bonus - $deduction, 2)),
                    'payment_method' => 'bank_transfer',
                    'payment_date' => Carbon::create($year, $month, 1)->addMonth()->addDays(mt_rand(0, 6))->toDateString(),
                    'notes' => $bonus ? 'Performance bonus included' : null,
                    'paid_by' => $admin?->id,
                ]);
            }
        }
    }

    /**
     * Working days (not off day / holiday) of a month for this user.
     */
    private function workingDays(User $user, int $year, int $month, bool $untilToday = false): array
    {
        $holidays = Holiday::forMonth($year, $month)->pluck('holiday_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())->flip();

        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        if ($untilToday && $end->gt(now()->subDay())) {
            $end = now()->subDay()->startOfDay();
        }

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if (! $user->isOffDay($d) && ! $holidays->has($d->toDateString())) {
                $days[] = $d->toDateString();
            }
        }

        return $days;
    }
}
