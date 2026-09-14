<?php

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

test('employee can view their own slot chain records and history', function () {
    $employee = User::factory()->create(['role' => 'employee', 'name' => 'John Employee']);
    $today = now()->toDateString();

    $tracking = DailySlotTracking::create([
        'user_id' => $employee->id,
        'tracking_date' => $today,
        'slot1_checkin_time' => now()->subHours(4),
        'slot2_checkin_time' => now()->subHours(2),
    ]);

    Livewire::actingAs($employee)
        ->test('employee::attendance-logs')
        ->assertSee('Working Hours & Slot History')
        ->assertSee('Today')
        ->call('openDetailModal', $tracking->id)
        ->assertSet('showDetailModal', true)
        ->assertSee('Slot Chain Details for');
});
