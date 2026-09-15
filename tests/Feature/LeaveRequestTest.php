<?php

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('employee can submit single day and multiple day leave requests', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    // Single day request
    Livewire::actingAs($employee)
        ->test('employee::leave-requests')
        ->set('request_type', 'single_day')
        ->set('start_date', '2026-09-20')
        ->set('end_date', '2026-09-20')
        ->set('reason', 'Fever and medical checkup')
        ->call('submitLeaveRequest')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $employee->id,
        'request_type' => 'single_day',
        'start_date' => '2026-09-20 00:00:00',
        'end_date' => '2026-09-20 00:00:00',
        'total_days' => 1,
        'status' => 'pending',
    ]);

    // Half day request
    Livewire::actingAs($employee)
        ->test('employee::leave-requests')
        ->set('request_type', 'half_day')
        ->set('start_date', '2026-09-21')
        ->set('end_date', '2026-09-21')
        ->set('reason', 'Dentist appointment afternoon')
        ->call('submitLeaveRequest')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $employee->id,
        'request_type' => 'half_day',
        'start_date' => '2026-09-21 00:00:00',
        'end_date' => '2026-09-21 00:00:00',
        'total_days' => 0.5,
        'status' => 'pending',
    ]);

    // Multiple days request
    Livewire::actingAs($employee)
        ->test('employee::leave-requests')
        ->set('request_type', 'multiple_days')
        ->set('start_date', '2026-09-22')
        ->set('end_date', '2026-09-24')
        ->set('reason', 'Family function out of station')
        ->call('submitLeaveRequest')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $employee->id,
        'request_type' => 'multiple_days',
        'start_date' => '2026-09-22 00:00:00',
        'end_date' => '2026-09-24 00:00:00',
        'total_days' => 3,
        'status' => 'pending',
    ]);
});

test('admin can cancel a leave request via confirmation check modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    $leaveRequest = LeaveRequest::create([
        'user_id' => $employee->id,
        'request_type' => 'single_day',
        'start_date' => '2026-09-20',
        'end_date' => '2026-09-20',
        'total_days' => 1,
        'reason' => 'Personal work',
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::leave-requests')
        ->call('openCancelModal', $leaveRequest->id)
        ->assertSet('showCancelModal', true)
        ->set('confirmCancelCheck', true)
        ->call('confirmCancelLeave')
        ->assertSet('showCancelModal', false);

    $this->assertDatabaseMissing('leave_requests', ['id' => $leaveRequest->id]);
});

test('admin can approve leave request as half day leave and generate half_day attendance record', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    $leaveRequest = LeaveRequest::create([
        'user_id' => $employee->id,
        'request_type' => 'single_day',
        'start_date' => '2026-09-25',
        'end_date' => '2026-09-25',
        'total_days' => 1,
        'reason' => 'Half day afternoon leave',
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::leave-requests')
        ->call('openReviewModal', $leaveRequest->id)
        ->set('decision', 'approved')
        ->set('leave_category', 'half_day')
        ->call('saveReview')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('leave_requests', [
        'id' => $leaveRequest->id,
        'status' => 'approved',
        'leave_category' => 'half_day',
        'reviewed_by' => $admin->id,
    ]);

    $this->assertDatabaseHas('attendances', [
        'user_id' => $employee->id,
        'attendance_date' => '2026-09-25 00:00:00',
        'status' => 'half_day',
        'leave_request_id' => $leaveRequest->id,
        'leave_category' => 'half_day',
    ]);
});

test('admin can reject a leave request', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    $leaveRequest = LeaveRequest::create([
        'user_id' => $employee->id,
        'request_type' => 'single_day',
        'start_date' => '2026-09-28',
        'end_date' => '2026-09-28',
        'total_days' => 1,
        'reason' => 'Unplanned day off',
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test('admin::leave-requests')
        ->call('openReviewModal', $leaveRequest->id)
        ->set('decision', 'rejected')
        ->set('admin_remarks', 'Insufficient notice during critical sprint')
        ->call('saveReview')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('leave_requests', [
        'id' => $leaveRequest->id,
        'status' => 'rejected',
        'reviewed_by' => $admin->id,
    ]);

    $this->assertDatabaseMissing('attendances', [
        'user_id' => $employee->id,
        'attendance_date' => '2026-09-28',
    ]);
});

test('admin can access separate employee attendance and leave calendar page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('admin.employee-calendar', $employee->id));
    $response->assertStatus(200);

    Livewire::actingAs($admin)
        ->test('admin::employee-calendar', ['user' => $employee])
        ->assertSee($employee->name)
        ->call('openDateModal', '2026-09-15')
        ->assertSet('showDetailModal', true)
        ->assertSet('selectedDateForModal', '2026-09-15')
        ->call('closeDetailModal')
        ->assertSet('showDetailModal', false);
});
