<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'salary_month',
    'monthly_salary',
    'total_days',
    'week_off_days',
    'holiday_days',
    'working_days',
    'present_days',
    'paid_leave_days',
    'unpaid_leave_days',
    'absent_days',
    'payable_days',
    'per_day_salary',
    'calculated_salary',
    'bonus',
    'deduction',
    'net_salary',
    'payment_method',
    'payment_date',
    'notes',
    'paid_by',
])]
class SalaryPayment extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'salary_month' => 'date',
            'payment_date' => 'date',
            'monthly_salary' => 'decimal:2',
            'present_days' => 'float',
            'paid_leave_days' => 'float',
            'unpaid_leave_days' => 'float',
            'absent_days' => 'float',
            'payable_days' => 'float',
            'per_day_salary' => 'decimal:2',
            'calculated_salary' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
