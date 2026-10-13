<?php

namespace Database\Factories;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Holiday>
 */
class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'New Year Day',
                'Republic Day',
                'Holi',
                'Good Friday',
                'Eid al-Fitr',
                'Independence Day',
                'Gandhi Jayanti',
                'Diwali',
                'Christmas Day',
            ]),
            'holiday_date' => fake()->unique()->date(),
            'type' => fake()->randomElement(['public', 'company', 'optional']),
            'description' => fake()->sentence(),
        ];
    }
}
