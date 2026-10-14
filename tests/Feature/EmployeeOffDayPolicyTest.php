<?php

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('default employee has Sunday only off policy and working Saturdays', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'saturday_off_policy' => 'sunday_only',
    ]);

    // Sunday Sept 13, 2026
    expect($employee->isOffDay(Carbon::parse('2026-09-13')))->toBeTrue();

    // 1st Saturday Sept 5, 2026
    expect($employee->isOffDay(Carbon::parse('2026-09-05')))->toBeFalse();

    // 2nd Saturday Sept 12, 2026
    expect($employee->isOffDay(Carbon::parse('2026-09-12')))->toBeFalse();

    // 4th Saturday Sept 26, 2026
    expect($employee->isOffDay(Carbon::parse('2026-09-26')))->toBeFalse();
});

test('employee with 2nd and 4th Saturday off policy has off days on 2nd and 4th Saturdays', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'saturday_off_policy' => 'sunday_2nd_4th_saturday',
    ]);

    // Sunday Sept 13, 2026
    expect($employee->isOffDay(Carbon::parse('2026-09-13')))->toBeTrue();

    // 1st Saturday Sept 5, 2026 -> Working
    expect($employee->isOffDay(Carbon::parse('2026-09-05')))->toBeFalse();

    // 2nd Saturday Sept 12, 2026 -> OFF
    expect($employee->isOffDay(Carbon::parse('2026-09-12')))->toBeTrue();

    // 3rd Saturday Sept 19, 2026 -> Working
    expect($employee->isOffDay(Carbon::parse('2026-09-19')))->toBeFalse();

    // 4th Saturday Sept 26, 2026 -> OFF
    expect($employee->isOffDay(Carbon::parse('2026-09-26')))->toBeTrue();
});

test('admin can select saturday off policy when creating employee', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test('admin::employee-management')
        ->set('name', 'New Special Employee')
        ->set('email', 'special@company.com')
        ->set('password', 'password123')
        ->set('role', 'employee')
        ->set('saturday_off_policy', 'sunday_2nd_4th_saturday')
        ->call('createEmployee')
        ->assertHasNoErrors();

    $created = User::where('email', 'special@company.com')->first();
    expect($created)->not->toBeNull();
    expect($created->saturday_off_policy)->toBe('sunday_2nd_4th_saturday');
});

test('admin employee calendar displays total working days excluding holidays and off days', function () {
    Carbon::setTestNow('2026-09-15 10:00:00');
    $admin = User::factory()->create(['role' => 'admin']);

    // Employee 1: Sunday only off (September 2026 has 30 days, 4 Sundays)
    $employeeSundayOnly = User::factory()->create([
        'role' => 'employee',
        'saturday_off_policy' => 'sunday_only',
    ]);

    // Holiday on a weekday (Monday Sept 21)
    Holiday::create([
        'name' => 'Autumn Fest',
        'holiday_date' => '2026-09-21',
        'type' => 'public',
    ]);

    // 30 days - 4 Sundays - 1 Holiday = 25 working days
    Livewire::actingAs($admin)
        ->test('admin::employee-calendar', ['user' => $employeeSundayOnly])
        ->assertSet('currentYear', 2026)
        ->assertSet('currentMonth', 9)
        ->assertViewHas('totalWorkingDaysCount', 25)
        ->assertViewHas('offDaysCount', 4)
        ->assertViewHas('holidayDaysCount', 1)
        ->assertSee('Total Working Days:')
        ->assertSee('25');

    // Employee 2: Sunday & 2nd/4th Saturday off (September 2026 has 4 Sundays + 2 Saturdays = 6 off days)
    $employeeAltSat = User::factory()->create([
        'role' => 'employee',
        'saturday_off_policy' => 'sunday_2nd_4th_saturday',
    ]);

    // 30 days - 6 Off days - 1 Holiday = 23 working days
    Livewire::actingAs($admin)
        ->test('admin::employee-calendar', ['user' => $employeeAltSat])
        ->assertViewHas('totalWorkingDaysCount', 23)
        ->assertViewHas('offDaysCount', 6)
        ->assertViewHas('holidayDaysCount', 1)
        ->assertSee('23');
});
