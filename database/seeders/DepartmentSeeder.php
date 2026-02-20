<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::truncate(); // clear old data (dev only)

        $names = ['HR', 'Finance', 'IT', 'Marketing', 'Operations'];

        foreach ($names as $name) {
            Department::create(['name' => $name]);
        }
    }
}
