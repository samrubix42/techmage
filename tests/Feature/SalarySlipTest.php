<?php

use App\Models\SalaryPayment;
use App\Models\User;
use App\Support\AmountInWords;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function makeSlip(User $employee, array $overrides = []): SalaryPayment
{
    return SalaryPayment::create(array_merge([
        'user_id' => $employee->id,
        'salary_month' => '2026-10-01',
        'monthly_salary' => 26000,
        'total_days' => 31,
        'week_off_days' => 4,
        'holiday_days' => 1,
        'working_days' => 26,
        'present_days' => 20.5,
        'paid_leave_days' => 1,
        'unpaid_leave_days' => 1,
        'absent_days' => 3.5,
        'payable_days' => 21.5,
        'per_day_salary' => 1000,
        'calculated_salary' => 21500,
        'bonus' => 500,
        'deduction' => 0,
        'net_salary' => 22000,
        'payment_method' => 'bank_transfer',
        'payment_date' => '2026-11-05',
    ], $overrides));
}

test('employee sees only their own salary slips', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $other = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $mine = makeSlip($employee);
    makeSlip($other, ['net_salary' => 99999]);

    $this->actingAs($employee)->get(route('employee.salary-slips'))
        ->assertOk()
        ->assertSee('My Salary Slips')
        ->assertSee('October 2026')
        ->assertSee('22,000.00')
        ->assertDontSee('99,999.00');

    Livewire::actingAs($employee)
        ->test('employee::salary-slips')
        ->call('viewSlip', $mine->id)
        ->assertSee('Net Salary Paid')
        ->assertSee('Rupees Twenty Two Thousand Only');
});

test('employee can download own slip as pdf but not others', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $other = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $mine = makeSlip($employee);
    $theirs = makeSlip($other);

    $response = $this->actingAs($employee)->get(route('salary-slips.download', $mine));
    $response->assertOk()->assertHeader('content-type', 'application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attachment')->toContain('Oct-2026.pdf')
        ->and(substr($response->getContent(), 0, 4))->toBe('%PDF');

    $preview = $this->actingAs($employee)->get(route('salary-slips.download', ['payment' => $mine, 'preview' => 1]));
    $preview->assertOk();
    expect($preview->headers->get('content-disposition'))->toStartWith('inline');

    $this->actingAs($employee)->get(route('salary-slips.download', $theirs))->assertForbidden();
});

test('admin can download any slip and guests cannot', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $slip = makeSlip(User::factory()->create(['role' => 'employee']));

    $this->get(route('salary-slips.download', $slip))->assertRedirect(route('login'));
    $this->actingAs($admin)->get(route('salary-slips.download', $slip))->assertOk()->assertHeader('content-type', 'application/pdf');
});

test('amount is converted to words in indian format', function () {
    expect(AmountInWords::rupees(22000))->toBe('Rupees Twenty Two Thousand Only')
        ->and(AmountInWords::rupees(125430.5))->toBe('Rupees One Lakh Twenty Five Thousand Four Hundred Thirty and Fifty Paise Only')
        ->and(AmountInWords::rupees(0))->toBe('Rupees Zero Only');
});
