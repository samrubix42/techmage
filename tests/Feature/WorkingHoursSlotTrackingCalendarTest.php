<?php

use App\Models\DailySlotTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-15 12:00:00');
});

test('calculates working hours based on slot tracking minus lunch duration', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    // 1. Exactly 8 hours: 9h total span minus 60m lunch = 8h 0m
    $trackingFullDay = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-10',
        'slot1_checkin_time' => Carbon::parse('2026-09-10 09:00:00'),
        'slot2_checkin_time' => Carbon::parse('2026-09-10 11:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-10 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-10 14:00:00'),
        'lunch_duration_minutes' => 60,
        'slot3_start_time' => Carbon::parse('2026-09-10 14:00:00'),
        'slot4_checkin_time' => Carbon::parse('2026-09-10 18:00:00'),
    ]);

    $workedMinutes = $trackingFullDay->getWorkedMinutes();
    expect($workedMinutes)->toBe(480); // 8 hours = 480 minutes
    expect(DailySlotTracking::formatMinutesToHours($workedMinutes))->toBe('8h 0m');
    expect(DailySlotTracking::isUnderEightHours($workedMinutes))->toBeFalse();

    // 2. Short day (7 hours): 8h total span minus 60m lunch = 7h 0m
    $trackingShortDay = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-11',
        'slot1_checkin_time' => Carbon::parse('2026-09-11 09:00:00'),
        'slot2_checkin_time' => Carbon::parse('2026-09-11 11:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-11 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-11 14:00:00'),
        'lunch_duration_minutes' => 60,
        'slot3_start_time' => Carbon::parse('2026-09-11 14:00:00'),
        'slot4_checkin_time' => Carbon::parse('2026-09-11 17:00:00'),
    ]);

    $shortMinutes = $trackingShortDay->getWorkedMinutes();
    expect($shortMinutes)->toBe(420); // 7 hours = 420 minutes
    expect(DailySlotTracking::formatMinutesToHours($shortMinutes))->toBe('7h 0m');
    expect(DailySlotTracking::isUnderEightHours($shortMinutes))->toBeTrue();

    // 3. Exceeded lunch: 9h total span minus 90m lunch = 7h 30m (under 8 hrs)
    $trackingExceededLunch = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-12',
        'slot1_checkin_time' => Carbon::parse('2026-09-12 09:00:00'),
        'slot2_checkin_time' => Carbon::parse('2026-09-12 11:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-12 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-12 14:30:00'),
        'lunch_duration_minutes' => 90,
        'lunch_exceeded' => true,
        'slot3_start_time' => Carbon::parse('2026-09-12 14:30:00'),
        'slot4_checkin_time' => Carbon::parse('2026-09-12 18:00:00'),
    ]);

    $exceededMinutes = $trackingExceededLunch->getWorkedMinutes();
    expect($exceededMinutes)->toBe(450); // 7h 30m = 450 minutes
    expect(DailySlotTracking::formatMinutesToHours($exceededMinutes))->toBe('7h 30m');
    expect(DailySlotTracking::isUnderEightHours($exceededMinutes))->toBeTrue();
});

test('employee calendar displays working hours and highlights red when less than 8 hours', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    // Short Day: Sept 8, 2026 -> 7h 0m (< 8h, should be RED)
    DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-08',
        'slot1_checkin_time' => Carbon::parse('2026-09-08 09:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-08 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-08 14:00:00'),
        'lunch_duration_minutes' => 60,
        'slot4_checkin_time' => Carbon::parse('2026-09-08 17:00:00'),
    ]);

    // Full Day: Sept 9, 2026 -> 8h 30m (>= 8h, should be GREEN/EMERALD)
    DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-09',
        'slot1_checkin_time' => Carbon::parse('2026-09-09 09:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-09 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-09 14:00:00'),
        'lunch_duration_minutes' => 60,
        'slot4_checkin_time' => Carbon::parse('2026-09-09 18:30:00'),
    ]);

    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->assertViewHas('calendarDays', function ($days) {
            $shortDay = collect($days)->firstWhere('date', '2026-09-08');
            $fullDay = collect($days)->firstWhere('date', '2026-09-09');

            return $shortDay
                && $shortDay['formattedHours'] === '7h 0m'
                && $shortDay['isUnderEightHours'] === true
                && $fullDay
                && $fullDay['formattedHours'] === '8h 30m'
                && $fullDay['isUnderEightHours'] === false;
        })
        ->assertSee('7h 0m')
        ->assertSee('8h 30m')
        ->call('openDateModal', '2026-09-08')
        ->assertSet('showDetailModal', true)
        ->assertSee('7h 0m')
        ->assertSee('< 8h');
});

test('admin employee calendar displays working hours and highlights red when less than 8 hours', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    // Short Day: Sept 8, 2026 -> 7h 0m (< 8h, should be RED)
    DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-08',
        'slot1_checkin_time' => Carbon::parse('2026-09-08 09:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-08 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-08 14:00:00'),
        'lunch_duration_minutes' => 60,
        'slot4_checkin_time' => Carbon::parse('2026-09-08 17:00:00'),
    ]);

    // Full Day: Sept 9, 2026 -> 8h 0m (>= 8h, should be EMERALD)
    DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => '2026-09-09',
        'slot1_checkin_time' => Carbon::parse('2026-09-09 09:00:00'),
        'lunch_start_time' => Carbon::parse('2026-09-09 13:00:00'),
        'lunch_end_time' => Carbon::parse('2026-09-09 14:00:00'),
        'lunch_duration_minutes' => 60,
        'slot4_checkin_time' => Carbon::parse('2026-09-09 18:00:00'),
    ]);

    Livewire::actingAs($admin)
        ->test('admin::employee-calendar', ['user' => $employee])
        ->assertViewHas('calendarDays', function ($days) {
            $shortDay = collect($days)->firstWhere('date', '2026-09-08');
            $fullDay = collect($days)->firstWhere('date', '2026-09-09');

            return $shortDay
                && $shortDay['formattedHours'] === '7h 0m'
                && $shortDay['isUnderEightHours'] === true
                && $fullDay
                && $fullDay['formattedHours'] === '8h 0m'
                && $fullDay['isUnderEightHours'] === false;
        })
        ->assertSee('7h 0m')
        ->assertSee('8h 0m')
        ->call('openDateModal', '2026-09-08')
        ->assertSet('showDetailModal', true)
        ->assertSee('7h 0m')
        ->assertSee('< 8 hrs');
});
