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

test('admin can view calendar grid and navigate months in holiday management', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Holiday::create([
        'name' => 'Diwali Day',
        'holiday_date' => '2026-11-08',
        'type' => 'public',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->assertSet('viewMode', 'calendar')
        ->assertSet('calendarMonth', 10)
        ->assertSet('calendarYear', 2026)
        ->assertSee('October 2026')
        ->call('nextMonth')
        ->assertSet('calendarMonth', 11)
        ->assertSee('November 2026')
        ->assertSee('Diwali Day')
        ->call('previousMonth')
        ->assertSet('calendarMonth', 10)
        ->assertSee('October 2026')
        ->call('goToToday')
        ->assertSet('calendarMonth', 10)
        ->assertSet('calendarYear', 2026);
});

test('admin can switch view mode between calendar and list', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->assertSet('viewMode', 'calendar')
        ->call('setViewMode', 'list')
        ->assertSet('viewMode', 'list')
        ->assertSee('Search holiday by title or description')
        ->call('setViewMode', 'calendar')
        ->assertSet('viewMode', 'calendar')
        ->assertSee('Holiday Legend:');
});

test('admin can open create modal with prefilled date by clicking calendar day', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openCreateModalForDate', '2026-10-25')
        ->assertSet('showCreateModal', true)
        ->assertSet('holiday_date', '2026-10-25')
        ->set('name', 'Custom Off Day')
        ->set('type', 'company')
        ->call('createHoliday')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    expect(Holiday::where('name', 'Custom Off Day')->exists())->toBeTrue();
    $created = Holiday::where('name', 'Custom Off Day')->first();
    expect($created->holiday_date->toDateString())->toBe('2026-10-25')
        ->and($created->type)->toBe('company');
});

test('admin can create multiple holidays using date range', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openCreateModal')
        ->set('date_mode', 'range')
        ->set('name', 'Diwali Vacation')
        ->set('start_date', '2026-11-01')
        ->set('end_date', '2026-11-04')
        ->set('type', 'public')
        ->call('createHoliday')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    expect(Holiday::where('name', 'Diwali Vacation')->count())->toBe(4);
    expect(Holiday::whereDate('holiday_date', '2026-11-01')->exists())->toBeTrue();
    expect(Holiday::whereDate('holiday_date', '2026-11-02')->exists())->toBeTrue();
    expect(Holiday::whereDate('holiday_date', '2026-11-03')->exists())->toBeTrue();
    expect(Holiday::whereDate('holiday_date', '2026-11-04')->exists())->toBeTrue();
});

test('admin can create multiple holidays using specific multiple dates list', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test('admin::holiday-management')
        ->call('openCreateModal')
        ->set('date_mode', 'multiple')
        ->set('name', 'National Observances')
        ->set('new_multiple_date', '2026-08-15')
        ->call('addMultipleDate')
        ->assertSet('multiple_dates', ['2026-08-15'])
        ->set('new_multiple_date', '2026-10-02')
        ->call('addMultipleDate')
        ->assertSet('multiple_dates', ['2026-08-15', '2026-10-02'])
        ->call('createHoliday')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    expect(Holiday::where('name', 'National Observances')->count())->toBe(2);
    expect(Holiday::whereDate('holiday_date', '2026-08-15')->exists())->toBeTrue();
    expect(Holiday::whereDate('holiday_date', '2026-10-02')->exists())->toBeTrue();
});
