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
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'title_description' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
