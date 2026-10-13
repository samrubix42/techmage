<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            [
                'name' => 'New Year\'s Day',
                'holiday_date' => '2026-01-01',
                'type' => 'public',
                'description' => 'Celebration of the first day of the new year.',
            ],
            [
                'name' => 'Republic Day',
                'holiday_date' => '2026-01-26',
                'type' => 'public',
                'description' => 'National holiday honoring the date on which the Constitution came into effect.',
            ],
            [
                'name' => 'Maha Shivratri',
                'holiday_date' => '2026-02-15',
                'type' => 'public',
                'description' => 'Hindu festival celebrated in reverence of Lord Shiva.',
            ],
            [
                'name' => 'Holi',
                'holiday_date' => '2026-03-04',
                'type' => 'public',
                'description' => 'Festival of colors celebrating the victory of good over evil.',
            ],
            [
                'name' => 'Good Friday',
                'holiday_date' => '2026-04-03',
                'type' => 'public',
                'description' => 'Christian holiday commemorating the crucifixion of Jesus.',
            ],
            [
                'name' => 'Labor Day',
                'holiday_date' => '2026-05-01',
                'type' => 'company',
                'description' => 'International Workers\' Day.',
            ],
            [
                'name' => 'Independence Day',
                'holiday_date' => '2026-08-15',
                'type' => 'public',
                'description' => 'National holiday celebrating independence.',
            ],
            [
                'name' => 'Gandhi Jayanti',
                'holiday_date' => '2026-10-02',
                'type' => 'public',
                'description' => 'National holiday marking Mahatma Gandhi\'s birthday.',
            ],
            [
                'name' => 'Diwali',
                'holiday_date' => '2026-11-08',
                'type' => 'public',
                'description' => 'Festival of Lights symbolizing spiritual victory.',
            ],
            [
                'name' => 'Christmas Day',
                'holiday_date' => '2026-12-25',
                'type' => 'public',
                'description' => 'Annual festival commemorating the birth of Jesus Christ.',
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                ['holiday_date' => $holiday['holiday_date']],
                $holiday
            );
        }
    }
}
