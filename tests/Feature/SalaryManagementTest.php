<?php

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Services\SalaryCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-11-10 10:00:00');
});

/**
 * October 2026: 31 days, 4 Sundays, 1 holiday (Oct 2) => 26 working days.
 * 20 present, 1 paid leave, 1 unpaid leave, 1 half day, 3 absent.
 * Also worked on Sunday Oct 4 (must NOT be counted).
 */
function seedOctoberAttendance(User $employee): void
{
    Holiday::create(['name' => 'Gandhi Jayanti', 'holiday_date' => '2026-10-02', 'type' => 'public']);

    $presentDays = [1, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 19, 20, 21, 22, 23, 24];
    foreach ($presentDays as $day) {
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => sprintf('2026-10-%02d', $day),
            'status' => 'present',
        ]);
    }

    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-26', 'status' => 'on_leave', 'leave_category' => 'sick', 'leave_payment_type' => 'paid']);
    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-27', 'status' => 'on_leave', 'leave_category' => 'unpaid', 'leave_payment_type' => 'unpaid']);
    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-28', 'status' => 'half_day']);
}

test('salary calculator excludes holidays and week offs and counts leaves', function () {
    $employee = User::factory()->create(['role' => 'employee', 'salary' => 26000, 'saturday_off_policy' => 'sunday_only']);
    seedOctoberAttendance($employee);

    $result = app(SalaryCalculator::class)->calculate($employee, 2026, 10);

    expect($result['total_days'])->toBe(31)
        ->and($result['week_off_days'])->toBe(4)
        ->and($result['holiday_days'])->toBe(1)
        ->and($result['working_days'])->toBe(26)
        ->and($result['present_days'])->toBe(20.5)
        ->and($result['paid_leave_days'])->toBe(1.0)
        ->and($result['unpaid_leave_days'])->toBe(1.0)
        ->and($result['leave_days'])->toBe(2.0)
        ->and($result['absent_days'])->toBe(3.5)
        ->and($result['payable_days'])->toBe(21.5)
        ->and($result['per_day_salary'])->toBe(1000.0)
        ->and($result['calculated_salary'])->toBe(21500.0);
});

test('paid leave has no monthly limit and leave on week offs or holidays is not counted', function () {
    $employee = User::factory()->create(['role' => 'employee', 'salary' => 26000, 'saturday_off_policy' => 'sunday_only']);
    Holiday::create(['name' => 'Gandhi Jayanti', 'holiday_date' => '2026-10-02', 'type' => 'public']);

    foreach (['05', '06', '07', '08'] as $day) {
        Attendance::create(['user_id' => $employee->id, 'attendance_date' => "2026-10-{$day}", 'status' => 'on_leave', 'leave_category' => 'sick', 'leave_payment_type' => 'paid']);
    }
    // Leave on a holiday (Oct 2) and a Sunday (Oct 4) must be ignored.
    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-02', 'status' => 'on_leave', 'leave_category' => 'sick', 'leave_payment_type' => 'paid']);
    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-04', 'status' => 'on_leave', 'leave_category' => 'sick', 'leave_payment_type' => 'paid']);
    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-09', 'status' => 'on_leave', 'leave_category' => 'unpaid', 'leave_payment_type' => 'unpaid']);

    $result = app(SalaryCalculator::class)->calculate($employee, 2026, 10);

    expect($result['paid_leave_days'])->toBe(4.0)
        ->and($result['unpaid_leave_days'])->toBe(1.0)
        ->and($result['payable_days'])->toBe(4.0)
        ->and(collect($result['leaves'])->pluck('date')->all())->not->toContain('2026-10-02', '2026-10-04');
});

test('admin can pay salary and the record is stored once per month', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee', 'salary' => 26000]);
    seedOctoberAttendance($employee);

    Livewire::actingAs($admin)
        ->test('admin::salary-management')
        ->assertSet('selectedMonth', '2026-10')
        ->call('openSalaryModal', $employee->id)
        ->assertSet('showSalaryModal', true)
        ->assertSee('Total Working Days')
        ->set('bonus', 500)
        ->set('deduction', 200)
        ->call('paySalary')
        ->assertHasNoErrors()
        ->assertSet('showSalaryModal', false);

    $payment = SalaryPayment::first();
    expect($payment)->not->toBeNull()
        ->and($payment->user_id)->toBe($employee->id)
        ->and($payment->salary_month->toDateString())->toBe('2026-10-01')
        ->and((float) $payment->calculated_salary)->toBe(21500.0)
        ->and((float) $payment->net_salary)->toBe(21800.0)
        ->and($payment->paid_by)->toBe($admin->id);

    // Paying again for the same month does not create a duplicate
    Livewire::actingAs($admin)
        ->test('admin::salary-management')
        ->call('openSalaryModal', $employee->id)
        ->call('paySalary');

    expect(SalaryPayment::count())->toBe(1);
});

test('admin can edit a paid salary and optionally recalculate it', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee', 'salary' => 26000, 'saturday_off_policy' => 'sunday_only']);
    seedOctoberAttendance($employee);

    Livewire::actingAs($admin)
        ->test('admin::salary-management')
        ->call('openSalaryModal', $employee->id)
        ->call('paySalary');

    $payment = SalaryPayment::first();

    // Edit bonus/deduction/method without recalculating
    Livewire::actingAs($admin)
        ->test('admin::salary-management')
        ->call('editPayment', $payment->id)
        ->assertSet('editingPayment', true)
        ->assertSee('Edit Salary Payment')
        ->set('bonus', 1000)
        ->set('deduction', 300)
        ->set('payment_method', 'cash') // ignored: always bank transfer
        ->set('notes', 'Corrected')
        ->call('updatePayment')
        ->assertHasNoErrors()
        ->assertSet('editingPayment', false);

    $payment->refresh();
    expect((float) $payment->calculated_salary)->toBe(21500.0)
        ->and((float) $payment->net_salary)->toBe(22200.0)
        ->and($payment->payment_method)->toBe('bank_transfer')
        ->and($payment->notes)->toBe('Corrected');

    // Attendance corrected afterwards: the absent day Oct 29 becomes present
    Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-29', 'status' => 'present']);

    Livewire::actingAs($admin)
        ->test('admin::salary-management')
        ->call('editPayment', $payment->id)
        ->set('recalculate', true)
        ->call('updatePayment')
        ->assertHasNoErrors();

    $payment->refresh();
    expect((float) $payment->calculated_salary)->toBe(22500.0)
        ->and((float) $payment->net_salary)->toBe(23200.0)
        ->and(SalaryPayment::count())->toBe(1);
});

test('admin cannot pay an employee without a salary set', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee', 'salary' => 0]);

    Livewire::actingAs($admin)
        ->test('admin::salary-management')
        ->call('openSalaryModal', $employee->id)
        ->call('paySalary')
        ->assertHasErrors('bonus');

    expect(SalaryPayment::count())->toBe(0);
});

test('admin can set employee salary from employee management', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($admin)
        ->test('admin::employee-management')
        ->call('openEditModal', $employee->id)
        ->set('salary', 35000)
        ->call('updateEmployee')
        ->assertHasNoErrors();

    expect((float) $employee->fresh()->salary)->toBe(35000.0);
});

test('salary page is admin only', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($employee)->get(route('admin.salaries'))->assertRedirect(route('employee.dashboard'));
    $this->actingAs($admin)->get(route('admin.salaries'))->assertOk()->assertSee('Salary Management');
});
