<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\HolidayFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'holiday_date', 'type', 'description'])]
class Holiday extends Model
{
    /** @use HasFactory<HolidayFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
        ];
    }

    /**
     * Scope for a specific year and month.
     */
    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('holiday_date', $year)
            ->whereMonth('holiday_date', $month);
    }

    /**
     * Scope for a specific date range.
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        $start = $startDate instanceof Carbon ? $startDate->toDateString() : (string) $startDate;
        $end = $endDate instanceof Carbon ? $endDate->toDateString() : (string) $endDate;

        return $query->whereBetween('holiday_date', [$start, $end]);
    }
}
