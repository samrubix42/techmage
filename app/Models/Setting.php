<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['for_role', 'key', 'value'])]
class Setting extends Model
{
    /**
     * Get a setting value by key and role.
     */
    public static function get(string $key, mixed $default = null, string $forRole = 'employee'): mixed
    {
        $setting = static::where('for_role', $forRole)
            ->where('key', $key)
            ->first();

        return $setting !== null ? $setting->value : $default;
    }

    /**
     * Set/update a setting value by key and role.
     */
    public static function set(string $key, mixed $value, string $forRole = 'employee'): bool
    {
        return (bool) static::updateOrCreate(
            ['for_role' => $forRole, 'key' => $key],
            ['value' => (string) $value]
        );
    }
}
