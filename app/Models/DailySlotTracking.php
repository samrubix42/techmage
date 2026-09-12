<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySlotTracking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tracking_date' => 'date',
            'slot1_checkin_time' => 'datetime',
            'slot2_checkin_time' => 'datetime',
            'lunch_start_time' => 'datetime',
            'lunch_end_time' => 'datetime',
            'slot3_start_time' => 'datetime',
            'slot3_end_time' => 'datetime',
            'slot2_is_flagged' => 'boolean',
            'lunch_exceeded' => 'boolean',
            'slot3_is_flagged' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
