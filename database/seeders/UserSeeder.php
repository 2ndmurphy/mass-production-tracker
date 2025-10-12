<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === Roles ===
        $roles = ['Admin', 'Manager', 'Staff'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // === Departments ===
        $departments = ['Production', 'QC', 'Warehouse'];
        foreach ($departments as $deptName) {
            Department::firstOrCreate(['name' => $deptName]);
        }

        // === Users ===
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $staffRole = Role::where('name', 'Staff')->first();

        $prodDept = Department::where('name', 'Production')->first();
        $qcDept = Department::where('name', 'QC')->first();
        $whDept = Department::where('name', 'Warehouse')->first();

        User::updateOrCreate(
            ['email' => 'admin@massprod.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'department_id' => $prodDept->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@massprod.com'],
            [
                'name' => 'Production Manager',
                'password' => Hash::make('manager123'),
                'role_id' => $managerRole->id,
                'department_id' => $prodDept->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff.production@massprod.com'],
            [
                'name' => 'Production Staff',
                'password' => Hash::make('staff123'),
                'role_id' => $staffRole->id,
                'department_id' => $prodDept->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff.qc@massprod.com'],
            [
                'name' => 'QC Staff',
                'password' => Hash::make('staff123'),
                'role_id' => $staffRole->id,
                'department_id' => $qcDept->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff.warehouse@massprod.com'],
            [
                'name' => 'Warehouse Staff',
                'password' => Hash::make('staff123'),
                'role_id' => $staffRole->id,
                'department_id' => $whDept->id,
            ]
        );
    }
}
