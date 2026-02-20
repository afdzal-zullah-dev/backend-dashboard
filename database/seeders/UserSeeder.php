<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate(); // DEV only

        $departments = Department::all();

        // ======================
        // 1️⃣ ADMIN (HR Department)
        // ======================
        User::create([
            'name' => 'AK Optima Admin',
            'email' => 'admin@akoptima.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => $departments->where('name', 'HR')->first()->id,
        ]);

        // ======================
        // 2️⃣ MANAGERS (1 per department)
        // ======================
        $managerDepartments = ['HR', 'Finance', 'IT', 'Marketing'];

        foreach ($managerDepartments as $index => $deptName) {
            User::create([
                'name' => 'AK Optima Manager ' . ($index + 1),
                'email' => 'manager' . ($index + 1) . '@akoptima.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'department_id' => $departments->where('name', $deptName)->first()->id,
            ]);
        }

        // ======================
        // 3️⃣ EMPLOYEES (random department)
        // ======================
        for ($i = 1; $i <= 7; $i++) {
            User::create([
                'name' => "AK Optima Employee $i",
                'email' => "employee{$i}@akoptima.com",
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department_id' => $departments->random()->id,
            ]);
        }
    }
}