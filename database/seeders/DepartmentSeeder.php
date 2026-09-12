<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Engineering & Technology',
            'Human Resources',
            'Marketing & Sales',
            'Product Design',
            'Customer Operations',
            'Finance & Accounting',
        ];

        foreach ($departments as $name) {
            $slug = Str::slug($name);

            Department::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );
        }
    }
}
