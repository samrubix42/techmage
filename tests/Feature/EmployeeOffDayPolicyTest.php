<?php

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
