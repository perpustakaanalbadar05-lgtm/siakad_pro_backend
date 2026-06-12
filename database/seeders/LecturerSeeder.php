<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $faculties = Faculty::with('studyPrograms')->get();

        if ($faculties->isEmpty()) {
            $this->command->warn('⚠️  Jalankan FacultySeeder terlebih dahulu.');
            return;
        }

        $dosens = [
            ['full_name' => 'Dr. Ahmad Fauzi, M.Ag', 'front_title' => 'Dr.', 'back_title' => 'M.Ag', 'nidn' => '2001018001', 'gender' => 'L'],
            ['full_name' => 'Dra. Siti Aminah, M.Pd', 'front_title' => 'Dra.', 'back_title' => 'M.Pd', 'nidn' => '2002028001', 'gender' => 'P'],
            ['full_name' => 'H. Muhammad Ridwan, Lc., M.A', 'front_title' => 'H.', 'back_title' => 'Lc., M.A', 'nidn' => '2003038001', 'gender' => 'L'],
            ['full_name' => 'Dr. Hj. Nurul Hidayah, M.Si', 'front_title' => 'Dr. Hj.', 'back_title' => 'M.Si', 'nidn' => '2004048001', 'gender' => 'P'],
            ['full_name' => 'Drs. Moh. Saleh, M.Pd.I', 'front_title' => 'Drs.', 'back_title' => 'M.Pd.I', 'nidn' => '2005058001', 'gender' => 'L'],
            ['full_name' => 'Dr. Fatimah Az-Zahra, M.Hum', 'front_title' => 'Dr.', 'back_title' => 'M.Hum', 'nidn' => '2006068001', 'gender' => 'P'],
            ['full_name' => 'Prof. Dr. Abdul Karim, M.Ag', 'front_title' => 'Prof. Dr.', 'back_title' => 'M.Ag', 'nidn' => '2007078001', 'gender' => 'L'],
            ['full_name' => 'Dra. Khadijah Rahmawati, M.Kes', 'front_title' => 'Dra.', 'back_title' => 'M.Kes', 'nidn' => '2008088001', 'gender' => 'P'],
            ['full_name' => 'Dr. Zainal Abidin, S.H., M.H', 'front_title' => 'Dr.', 'back_title' => 'S.H., M.H', 'nidn' => '2009098001', 'gender' => 'L'],
            ['full_name' => 'Hj. Aisyah Mardiyah, M.E.Sy', 'front_title' => 'Hj.', 'back_title' => 'M.E.Sy', 'nidn' => '2010108001', 'gender' => 'P'],
            ['full_name' => 'Dr. Umar Harun, M.Pd', 'front_title' => 'Dr.', 'back_title' => 'M.Pd', 'nidn' => '2011118001', 'gender' => 'L'],
            ['full_name' => 'Dra. Maryam Badriyah, M.A', 'front_title' => 'Dra.', 'back_title' => 'M.A', 'nidn' => '2012128001', 'gender' => 'P'],
            ['full_name' => 'H. Ibrahim Khalil, M.Ag', 'front_title' => 'H.', 'back_title' => 'M.Ag', 'nidn' => '2013138001', 'gender' => 'L'],
            ['full_name' => 'Dr. Hj. Zulaikha, M.Pd.I', 'front_title' => 'Dr. Hj.', 'back_title' => 'M.Pd.I', 'nidn' => '2014148001', 'gender' => 'P'],
            ['full_name' => 'Drs. Mustofa Kamil, M.Si', 'front_title' => 'Drs.', 'back_title' => 'M.Si', 'nidn' => '2015158001', 'gender' => 'L'],
            ['full_name' => 'Dr. Rahmat Hidayatullah, M.Ag', 'front_title' => 'Dr.', 'back_title' => 'M.Ag', 'nidn' => '2016168001', 'gender' => 'L'],
            ['full_name' => 'Dra. Roudhotul Jannah, M.Pd', 'front_title' => 'Dra.', 'back_title' => 'M.Pd', 'nidn' => '2017178001', 'gender' => 'P'],
            ['full_name' => 'H. Syamsul Arifin, Lc., M.Th.I', 'front_title' => 'H.', 'back_title' => 'Lc., M.Th.I', 'nidn' => '2018188001', 'gender' => 'L'],
            ['full_name' => 'Dr. Hj. Nur Aini, M.E', 'front_title' => 'Dr. Hj.', 'back_title' => 'M.E', 'nidn' => '2019198001', 'gender' => 'P'],
            ['full_name' => 'Drs. Ahmad Ghozali, M.Pd.I', 'front_title' => 'Drs.', 'back_title' => 'M.Pd.I', 'nidn' => '2020208001', 'gender' => 'L'],
        ];

        $studyPrograms = StudyProgram::all();
        $progIndex = 0;

        foreach ($dosens as $i => $dosenData) {
            $program = $studyPrograms[$progIndex % $studyPrograms->count()];
            $progIndex++;

            $email = 'dosen' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '@iaimu.ac.id';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'dosen' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'full_name' => $dosenData['full_name'],
                    'email' => $email,
                    'password' => Hash::make('Dosen@1234'),
                    'status' => 'active',
                ]
            );
            $user->assignRole('dosen');

            Lecturer::firstOrCreate(
                ['nidn' => $dosenData['nidn']],
                array_merge($dosenData, [
                    'user_id' => $user->id,
                    'faculty_id' => $program->faculty_id,
                    'study_program_id' => $program->id,
                    'email' => $email,
                    'phone' => '08' . rand(100000000, 999999999),
                    'employment_status' => 'tetap',
                    'status' => 'active',
                    'birth_place' => 'Pamekasan',
                    'birth_date' => '1980-0' . rand(1, 9) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                ])
            );
        }

        $this->command->info('✅ 20 dosen dummy berhasil dibuat. Password: Dosen@1234');
    }
}
