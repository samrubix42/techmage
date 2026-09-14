<?php

use App\Models\Attendance;
use App\Models\DailySlotTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-14 14:00:00');
});

test('employee can view their own monthly calendar with present and leave badges', function () {
    $employee = User::factory()->create(['role' => 'employee', 'name' => 'John Employee']);
    $today = now()->toDateString();

    $tracking = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => $today,
        'slot1_checkin_time' => now()->subHours(4),
        'slot2_checkin_time' => now()->subHours(2),
    ]);

    Attendance::create([
        'user_id' => $employee->id,
        'attendance_date' => '2026-09-10',
        'status' => 'on_leave',
    ]);

    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->assertSee('Working Hours')
        ->assertSee('Calendar View')
        ->assertSee('September 2026')
        ->assertSee('On Leave')
        ->call('openDateModal', '2026-09-10')
        ->assertSet('showDetailModal', true)
        ->assertSee('Work Logs for');
});

test('employee can navigate calendar months and switch view modes', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->call('previousMonth')
        ->assertSee('August 2026')
        ->call('nextMonth')
        ->assertSee('September 2026')
        ->call('setViewMode', 'list')
        ->assertSet('viewMode', 'list')
        ->assertSee('Daily Slot Progress Chain');
});
