<?php

use App\Models\Setting;

if (! function_exists('get_setting')) {
    /**
     * Helper to retrieve a setting from the database.
     */
    function get_setting(string $key, mixed $default = null, string $forRole = 'employee'): mixed
    {
        return Setting::get($key, $default, $forRole);
    }
}

if (! function_exists('set_setting')) {
    /**
     * Helper to store/update a setting in the database.
     */
    function set_setting(string $key, mixed $value, string $forRole = 'employee'): bool
    {
        return Setting::set($key, $value, $forRole);
    }
}
