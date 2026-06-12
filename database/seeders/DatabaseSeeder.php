<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AcademicYearSeeder::class,
            FacultySeeder::class,
            LecturerSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
