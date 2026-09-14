<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTaskReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'title_description',
        'is_checked',
        'checked_at',
        'checked_by',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'title_description' => 'array',
            'is_checked' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
