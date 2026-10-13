<?php

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-10-15 10:00:00');
});

test('holiday displays on employee calendar and admin employee calendar', function () {
    $employee = User::factory()->create(['role' => 'employee', 'name' => 'John Employee']);
    $admin = User::factory()->create(['role' => 'admin']);

    $holiday = Holiday::create([
        'name' => 'Dussehra',
        'holiday_date' => '2026-10-20',
        'type' => 'public',
        'description' => 'Festive holiday',
    ]);

    // Test Employee Calendar
    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->assertViewHas('calendarDays', function ($days) {
            $day = collect($days)->firstWhere('date', '2026-10-20');

            return $day && $day['isHoliday'] === true && $day['status'] === 'holiday' && $day['holiday']->name === 'Dussehra';
        })
        ->assertSee('Dussehra')
        ->assertSee('Holidays:');

    // Test Admin Employee Calendar
    Livewire::actingAs($admin)
        ->test('admin::employee-calendar', ['user' => $employee])
        ->assertViewHas('calendarDays', function ($days) {
            $day = collect($days)->firstWhere('date', '2026-10-20');

            return $day && $day['isHoliday'] === true && $day['status'] === 'holiday' && $day['holiday']->name === 'Dussehra';
        })
        ->assertSee('Dussehra');
});

test('past holiday is not marked as absent when employee did not work', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    // Oct 10, 2026 is in the past (test now is Oct 15) and was a Friday/weekday
    Holiday::create([
        'name' => 'Special Holiday',
        'holiday_date' => '2026-10-09',
        'type' => 'public',
    ]);

    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->assertViewHas('calendarDays', function ($days) {
            $day = collect($days)->firstWhere('date', '2026-10-09');

            return $day && $day['status'] === 'holiday'; // Not 'absent'!
        });
});

test('employee can work on holiday and calendar reflects holiday worked status with hours', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'name' => 'Diligent Worker',
    ]);
    $admin = User::factory()->create(['role' => 'admin']);

    // Set holiday on Oct 15 (today)
    Holiday::create([
        'name' => 'Festive Holiday',
        'holiday_date' => '2026-10-15',
        'type' => 'public',
    ]);

    // Employee clocks in on holiday
    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('clockIn');

    // Add a completed log of 4 hours
    $attendance = Attendance::where('user_id', $employee->id)
        ->whereDate('attendance_date', '2026-10-15')
        ->first();

    expect($attendance)->not->toBeNull();

    AttendanceLog::where('attendance_id', $attendance->id)->update([
        'clock_in_time' => Carbon::parse('2026-10-15 09:00:00'),
        'clock_out_time' => Carbon::parse('2026-10-15 13:00:00'),
        'duration_minutes' => 240,
    ]);

    // Test Employee Calendar reflects holiday_worked
    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->assertViewHas('calendarDays', function ($days) {
            $day = collect($days)->firstWhere('date', '2026-10-15');

            return $day
                && $day['isHoliday'] === true
                && $day['status'] === 'holiday_worked'
                && $day['formattedHours'] === '4h 0m';
        })
        ->assertSee('Festive Holiday (Worked)')
        ->call('openDateModal', '2026-10-15')
        ->assertSet('showDetailModal', true)
        ->assertSee('You worked on this holiday');

    // Test Admin Employee Calendar reflects holiday_worked
    Livewire::actingAs($admin)
        ->test('admin::employee-calendar', ['user' => $employee])
        ->assertViewHas('calendarDays', function ($days) {
            $day = collect($days)->firstWhere('date', '2026-10-15');

            return $day
                && $day['isHoliday'] === true
                && $day['status'] === 'holiday_worked'
                && $day['formattedHours'] === '4h 0m';
        })
        ->assertSee('Worked')
        ->call('openDateModal', '2026-10-15')
        ->assertSet('showDetailModal', true)
        ->assertSee('Employee worked on this holiday');
});

test('admin holiday management view modal lists employees who worked on that holiday', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create([
        'role' => 'employee',
        'name' => 'Holiday Contributor',
    ]);

    $holiday = Holiday::create([
        'name' => 'National Holiday',
        'holiday_date' => '2026-10-12',
        'type' => 'public',
    ]);

    $attendance = Attendance::create([
        'user_id' => $employee->id,
        'attendance_date' => '2026-10-12',
        'status' => 'present',
        'clock_in_time' => Carbon::parse('2026-10-12 10:00:00'),
        'clock_out_time' => Carbon::parse('2026-10-12 16:00:00'),
    ]);

    AttendanceLog::create([
        'attendance_id' => $attendance->id,
        'user_id' => $employee->id,
        'clock_in_time' => Carbon::parse('2026-10-12 10:00:00'),
        'clock_out_time' => Carbon::parse('2026-10-12 16:00:00'),
        'duration_minutes' => 360,
    ]);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openViewModal', $holiday->id)
        ->assertSet('showViewModal', true)
        ->assertSee('Holiday Contributor')
        ->assertSee('6h 0m');
});

test('employee dashboard displays holiday banner and status when today is a holiday', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'is_active' => true,
    ]);

    Holiday::create([
        'name' => 'Independence Festival',
        'holiday_date' => '2026-10-15',
        'type' => 'public',
        'description' => 'Celebration day with optional work permitted.',
    ]);

    // Test before clock-in: shows holiday announcement and optional work note
    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->assertViewHas('todayHoliday', function ($h) {
            return $h && $h->name === 'Independence Festival';
        })
        ->assertSee('Today is an Official Holiday: Independence Festival')
        ->assertSee('Work is Optional Today')
        ->assertSee('Holiday (Not Clocked In)');

    // Clock in on holiday
    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('clockIn')
        ->assertSee('Working on Holiday');
});
