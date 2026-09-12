<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'is_active'])]
class Department extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot function to handle auto-slug generation.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Department $department) {
            if (empty($department->slug) && ! empty($department->name)) {
                $department->slug = Str::slug($department->name);
            }
        });

        static::updating(function (Department $department) {
            if (empty($department->slug) && ! empty($department->name)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }
}
