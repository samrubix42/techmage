<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_active', 'role', 'department_id', 'saturday_off_policy'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Determine if a specific date is an off day for this employee.
     */
    public function isOffDay($date): bool
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Sunday is always off for everyone in the company
        if ($carbon->isSunday()) {
            return true;
        }

        // Check Saturday policy
        if ($carbon->isSaturday()) {
            if ($this->saturday_off_policy === 'sunday_2nd_4th_saturday') {
                $day = $carbon->day;

                // 2nd Saturday (8-14) or 4th Saturday (22-28)
                return ($day >= 8 && $day <= 14) || ($day >= 22 && $day <= 28);
            }

            return false; // Only Sunday off policy -> Saturday is working
        }

        return false;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function slotTrackings(): HasMany
    {
        return $this->hasMany(DailySlotTracking::class);
    }

    public function todaySlotTracking()
    {
        return $this->hasOne(DailySlotTracking::class)->where('tracking_date', now()->toDateString());
    }

    public function dailyTaskReports(): HasMany
    {
        return $this->hasMany(DailyTaskReport::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
