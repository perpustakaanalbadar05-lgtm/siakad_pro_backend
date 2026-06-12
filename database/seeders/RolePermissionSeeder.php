<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================================================================
        // Define Permissions
        // =====================================================================
        $permissions = [
            // User Management
            'users.view', 'users.create', 'users.update', 'users.delete',

            // Faculty
            'faculties.view', 'faculties.create', 'faculties.update', 'faculties.delete',

            // Study Program
            'study_programs.view', 'study_programs.create', 'study_programs.update', 'study_programs.delete',

            // Academic Year
            'academic_years.view', 'academic_years.create', 'academic_years.update', 'academic_years.delete',

            // Curriculum & Courses
            'curriculums.view', 'curriculums.create', 'curriculums.update', 'curriculums.delete',
            'courses.view', 'courses.create', 'courses.update', 'courses.delete',

            // Students
            'students.view', 'students.create', 'students.update', 'students.delete', 'students.import', 'students.export',

            // Lecturers
            'lecturers.view', 'lecturers.create', 'lecturers.update', 'lecturers.delete',

            // Rooms & Buildings
            'buildings.view', 'buildings.create', 'buildings.update', 'buildings.delete',
            'rooms.view', 'rooms.create', 'rooms.update', 'rooms.delete',

            // Dashboard
            'dashboard.admin', 'dashboard.student', 'dashboard.lecturer',
            'dashboard.kaprodi', 'dashboard.dekan', 'dashboard.rektor',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // =====================================================================
        // Define Roles & Assign Permissions
        // =====================================================================

        // Super Admin - akses penuh
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Rektor - view semua data
        $rektor = Role::firstOrCreate(['name' => 'rektor']);
        $rektor->syncPermissions([
            'faculties.view', 'study_programs.view', 'students.view', 'lecturers.view',
            'academic_years.view', 'dashboard.rektor',
        ]);

        // Wakil Rektor
        $wakilRektor = Role::firstOrCreate(['name' => 'wakil_rektor']);
        $wakilRektor->syncPermissions([
            'faculties.view', 'study_programs.view', 'students.view', 'lecturers.view',
            'academic_years.view', 'dashboard.rektor',
        ]);

        // Dekan
        $dekan = Role::firstOrCreate(['name' => 'dekan']);
        $dekan->syncPermissions([
            'study_programs.view', 'students.view', 'lecturers.view',
            'courses.view', 'dashboard.dekan',
        ]);

        // Kaprodi
        $kaprodi = Role::firstOrCreate(['name' => 'kaprodi']);
        $kaprodi->syncPermissions([
            'students.view', 'students.export',
            'lecturers.view',
            'curriculums.view', 'curriculums.create', 'curriculums.update',
            'courses.view', 'courses.create', 'courses.update',
            'dashboard.kaprodi',
        ]);

        // Admin Akademik
        $adminAkademik = Role::firstOrCreate(['name' => 'admin_akademik']);
        $adminAkademik->syncPermissions([
            'faculties.view', 'study_programs.view',
            'students.view', 'students.create', 'students.update', 'students.import', 'students.export',
            'lecturers.view', 'lecturers.create', 'lecturers.update',
            'academic_years.view', 'academic_years.create', 'academic_years.update',
            'curriculums.view', 'curriculums.create', 'curriculums.update',
            'courses.view', 'courses.create', 'courses.update',
            'buildings.view', 'buildings.create', 'buildings.update',
            'rooms.view', 'rooms.create', 'rooms.update',
            'dashboard.admin',
        ]);

        // Admin Fakultas
        $adminFakultas = Role::firstOrCreate(['name' => 'admin_fakultas']);
        $adminFakultas->syncPermissions([
            'students.view', 'lecturers.view', 'study_programs.view',
        ]);

        // Admin Prodi
        $adminProdi = Role::firstOrCreate(['name' => 'admin_prodi']);
        $adminProdi->syncPermissions([
            'students.view', 'courses.view', 'curriculums.view',
        ]);

        // Dosen
        $dosen = Role::firstOrCreate(['name' => 'dosen']);
        $dosen->syncPermissions([
            'students.view', 'courses.view', 'dashboard.lecturer',
        ]);

        // Mahasiswa
        $mahasiswa = Role::firstOrCreate(['name' => 'mahasiswa']);
        $mahasiswa->syncPermissions([
            'dashboard.student',
        ]);

        // Keuangan
        $keuangan = Role::firstOrCreate(['name' => 'keuangan']);
        $keuangan->syncPermissions([
            'students.view', 'students.export',
        ]);

        // PMB
        Role::firstOrCreate(['name' => 'pmb']);

        // Perpustakaan
        Role::firstOrCreate(['name' => 'perpustakaan']);

        // Alumni
        Role::firstOrCreate(['name' => 'alumni']);

        // Orang Tua/Wali
        Role::firstOrCreate(['name' => 'orang_tua']);

        // =====================================================================
        // Buat Super Admin User
        // =====================================================================
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@iaimu.ac.id'],
            [
                'name' => 'superadmin',
                'full_name' => 'Super Administrator SIAKAD',
                'email' => 'admin@iaimu.ac.id',
                'password' => Hash::make('Admin@1234'),
                'status' => 'active',
            ]
        );
        $adminUser->assignRole('super_admin');

        $this->command->info('✅ Roles dan permissions berhasil dibuat.');
        $this->command->info('📧 Super Admin: admin@iaimu.ac.id / Admin@1234');
    }
}
