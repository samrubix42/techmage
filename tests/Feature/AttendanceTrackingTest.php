<?php

use App\Models\DailySlotTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-12 14:00:00');
});

test('employee can clock in and 2-hr checkin is disabled before 1.5 hrs', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('clockIn')
        ->assertSee('Clocked in at');

    $tracking = DailySlotTracking::where('user_id', $employee->id)->first();
    expect($tracking)->not->toBeNull();
    expect($tracking->slot1_checkin_time)->not->toBeNull();

    // Trying 2-hr checkin immediately should be rejected
    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('save2HrCheckin')
        ->assertSee('Slot 2 check-in is disabled until 1.5 hours have elapsed');
});

test('employee can complete 2-hr checkin after 1.5 hrs and flags red if exceeded past 2.5 hrs', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $today = now()->toDateString();

    // Checked in 160 minutes ago (target 120m, exceeded 30m window by 40m)
    $tracking = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => $today,
        'slot1_checkin_time' => now()->subMinutes(160),
    ]);

    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('save2HrCheckin')
        ->assertSee('Exceeded 30-min window');

    $tracking->refresh();
    expect($tracking->slot2_is_flagged)->toBeTrue();
    expect($tracking->slot2_deviation_minutes)->toBe(40);
});

test('detects exceeded lunch over 60 mins and shows red flag in admin', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $today = now()->toDateString();

    $tracking = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => $today,
        'slot1_checkin_time' => now()->subHours(4),
        'slot2_checkin_time' => now()->subHours(2),
        'lunch_start_time' => now()->subMinutes(80), // 80 mins lunch (exceeded by 20m)
    ]);

    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('endLunchAndStartSlot3')
        ->assertSee('Exceeded 1-hour limit by 20 mins');

    $tracking->refresh();
    expect($tracking->lunch_exceeded)->toBeTrue();
    expect($tracking->lunch_exceeded_minutes)->toBe(20);

    $admin = User::factory()->create(['role' => 'admin']);
    Livewire::actingAs($admin)
        ->test('admin::dashboard')
        ->assertSee('Exceeded by +20m')
        ->assertSee('Red Flagged');
});

test('flags 3rd slot timing deviation if exceeded past 30 minutes', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $today = now()->toDateString();

    $tracking = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => $today,
        'slot1_checkin_time' => now()->subHours(7),
        'slot2_checkin_time' => now()->subHours(5),
        'lunch_start_time' => now()->subHours(4),
        'lunch_end_time' => now()->subHours(3),
        'lunch_duration_minutes' => 60,
        'slot3_start_time' => now()->subMinutes(160), // Worked 160 mins (exceeded 120m target by 40m > 30m)
    ]);

    Livewire::actingAs($employee)
        ->test('employee::dashboard')
        ->call('saveSlot3AndClockOut')
        ->assertSee('Exceeded 3rd slot');

    $tracking->refresh();
    expect($tracking->slot3_is_flagged)->toBeTrue();
    expect($tracking->slot3_timing_status)->toBe('exceeded');
    expect($tracking->slot3_deviation_minutes)->toBe(40);

    $admin = User::factory()->create(['role' => 'admin']);
    Livewire::actingAs($admin)
        ->test('admin::dashboard')
        ->assertSee('Exceeded Slot 3 (+40m)')
        ->assertSee('Red Flagged');
});

test('it syncs 1st slot time on clock in from sidebar', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test('employee.sidebar')
        ->call('clockIn');

    $tracking = \App\Models\DailySlotTracking::where('user_id', $employee->id)
        ->whereDate('tracking_date', now()->toDateString())
        ->first();

    expect($tracking)->not->toBeNull();
    expect($tracking->slot1_checkin_time)->not->toBeNull();
});
