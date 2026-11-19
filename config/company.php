<?php

/*
|--------------------------------------------------------------------------
| Company details
|--------------------------------------------------------------------------
|
| Shown on generated documents such as salary slips. Override any value
| from .env (e.g. COMPANY_ADDRESS="..." ) without touching the code.
|
*/

return [
    'name' => env('COMPANY_NAME', 'Techonika'),
    'address' => env('COMPANY_ADDRESS', ''),
    'email' => env('COMPANY_EMAIL', ''),
    'phone' => env('COMPANY_PHONE', ''),
    'website' => env('COMPANY_WEBSITE', 'www.techonika.in'),
    'logo' => env('COMPANY_LOGO', 'logo.png'), // path inside /public
];
