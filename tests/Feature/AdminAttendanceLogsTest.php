<?php

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\DailySlotTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-14 12:00:00');
});

test('admin can view attendance logs page and see employee work stats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee', 'name' => 'Alice Johnson']);

    $today = now()->toDateString();
    $attendance = Attendance::create([
        'user_id' => $employee->id,
        'attendance_date' => $today,
        'status' => 'present',
        'clock_in_time' => now()->subHours(4),
        'clock_out_time' => now(),
    ]);

    AttendanceLog::create([
        'attendance_id' => $attendance->id,
        'user_id' => $employee->id,
        'clock_in_time' => now()->subHours(4),
        'clock_out_time' => now()->subHours(2),
        'duration_minutes' => 120,
    ]);

    AttendanceLog::create([
        'attendance_id' => $attendance->id,
        'user_id' => $employee->id,
        'clock_in_time' => now()->subHour(),
        'clock_out_time' => now(),
        'duration_minutes' => 60,
    ]);

    Livewire::actingAs($admin)
        ->test('admin::attendance-logs')
        ->assertSee('Alice Johnson')
        ->assertSee('3h 0m') // 120m + 60m = 180m = 3h 0m
        ->assertSee('1h 0m'); // 1 hour break between 2:00 ago and 1:00 ago
});

test('admin can filter by date and search employee', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee1 = User::factory()->create(['role' => 'employee', 'name' => 'John Doe']);
    $employee2 = User::factory()->create(['role' => 'employee', 'name' => 'Jane Smith']);

    Livewire::actingAs($admin)
        ->test('admin::attendance-logs')
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('admin can open modal to inspect working hours breakdown and progress chain', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee', 'name' => 'Robert Paulson']);

    $today = now()->toDateString();

    $attendance = Attendance::create([
        'user_id' => $employee->id,
        'attendance_date' => $today,
        'status' => 'present',
        'clock_in_time' => now()->subHours(5),
    ]);

    AttendanceLog::create([
        'attendance_id' => $attendance->id,
        'user_id' => $employee->id,
        'clock_in_time' => now()->subHours(5),
        'clock_out_time' => now()->subHours(1),
        'duration_minutes' => 240,
    ]);

    DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => $today,
        'slot1_checkin_time' => now()->subHours(5),
        'slot2_checkin_time' => now()->subHours(3),
        'lunch_start_time' => now()->subHours(2),
        'lunch_end_time' => now()->subHour(),
        'lunch_duration_minutes' => 60,
    ]);

    Livewire::actingAs($admin)
        ->test('admin::attendance-logs')
        ->call('openModal', $employee->id)
        ->assertSet('showDetailModal', true)
        ->assertSee('Total Worked')
        ->assertSee('Daily Slot Progress Chain')
        ->assertSee('Robert Paulson');
});
