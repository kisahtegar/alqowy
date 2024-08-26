<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::create([
            'name' => 'owner'
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher'
        ]);

        $studentRole = Role::create([
            'name' => 'student'
        ]);

        // AKun super admin untuk mengelola data awal seperti kategori, kelas dll
        $userOwner = User::create([
            'name' => 'Kisah Tegar',
            'occupation' => 'Educator',
            'avatar' => 'images/default-avatar.jpg',
            'email' => 'admin@example.com',
            'password' => bcrypt('123123123'),
        ]);

        $userOwner->assignRole($ownerRole);
    }
}
