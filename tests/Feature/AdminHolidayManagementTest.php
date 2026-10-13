<?php

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-10-13 10:00:00');
});

test('admin can access holiday management page and view holidays', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Holiday::create([
        'name' => 'Independence Day',
        'holiday_date' => '2026-08-15',
        'type' => 'public',
        'description' => 'National holiday',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.holidays'))
        ->assertOk()
        ->assertSee('Holiday Management')
        ->assertSee('Independence Day');
});

test('employee cannot access admin holiday management route', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $this->actingAs($employee)
        ->get(route('admin.holidays'))
        ->assertRedirect(route('employee.dashboard'));
});

test('admin can create a new holiday with validation', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('name', 'Diwali Celebration')
        ->set('holiday_date', '2026-11-08')
        ->set('type', 'public')
        ->set('description', 'Festival of Lights')
        ->call('createHoliday')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    expect(Holiday::where('name', 'Diwali Celebration')->exists())->toBeTrue();
    $holiday = Holiday::where('name', 'Diwali Celebration')->first();
    expect($holiday->holiday_date->toDateString())->toBe('2026-11-08')
        ->and($holiday->type)->toBe('public');
});

test('admin cannot create a holiday on a duplicate date', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Holiday::create([
        'name' => 'Existing Holiday',
        'holiday_date' => '2026-12-25',
        'type' => 'public',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->set('name', 'Christmas Day')
        ->set('holiday_date', '2026-12-25')
        ->set('type', 'public')
        ->call('createHoliday')
        ->assertHasErrors(['holiday_date']);

    expect(Holiday::where('name', 'Christmas Day')->exists())->toBeFalse();
});

test('admin can edit an existing holiday', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $holiday = Holiday::create([
        'name' => 'Founder Day',
        'holiday_date' => '2026-05-10',
        'type' => 'company',
        'description' => 'Initial note',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openEditModal', $holiday->id)
        ->assertSet('showEditModal', true)
        ->assertSet('name', 'Founder Day')
        ->set('name', 'Annual Company Founder Day')
        ->set('description', 'Updated note for company anniversary')
        ->call('updateHoliday')
        ->assertHasNoErrors()
        ->assertSet('showEditModal', false);

    $holiday->refresh();
    expect($holiday->name)->toBe('Annual Company Founder Day')
        ->and($holiday->description)->toBe('Updated note for company anniversary');
});

test('admin can delete a holiday', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $holiday = Holiday::create([
        'name' => 'Temporary Off Day',
        'holiday_date' => '2026-09-01',
        'type' => 'optional',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openDeleteModal', $holiday->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteHoliday')
        ->assertSet('showDeleteModal', false);

    expect(Holiday::find($holiday->id))->toBeNull();
});
