<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $studyPrograms = StudyProgram::with('faculty')->get();

        if ($studyPrograms->isEmpty()) {
            $this->command->warn('⚠️  Jalankan FacultySeeder terlebih dahulu.');
            return;
        }

        $namaLaki = ['Ahmad', 'Muhammad', 'Abdullah', 'Rizky', 'Fajar', 'Arif', 'Hasan', 'Husain', 'Ali', 'Umar', 'Syahrul', 'Fajrul', 'Ibnu', 'Fikri', 'Rahmat', 'Bagas', 'Dani', 'Eko', 'Fandi', 'Galih'];
        $namaPerempuan = ['Siti', 'Nur', 'Aisyah', 'Fatimah', 'Khadijah', 'Zahra', 'Maryam', 'Raudah', 'Humairah', 'Sabrina', 'Indah', 'Putri', 'Dewi', 'Laily', 'Naila', 'Rahma', 'Shofiyah', 'Ulfa', 'Vina', 'Wilda'];
        $namaBelakang = ['Pratama', 'Putra', 'Wijaya', 'Santoso', 'Rahman', 'Hidayat', 'Fauzi', 'Kurniawan', 'Setiawan', 'Nugroho', 'Rahmah', 'Hayati', 'Khairiyah', 'Maulida', 'Nadhifah', 'Oktavia', 'Permata', 'Ramadhani', 'Safitri', 'Tanjung'];
        $kotaLahir = ['Pamekasan', 'Sumenep', 'Sampang', 'Bangkalan', 'Surabaya', 'Malang', 'Jember', 'Situbondo', 'Bondowoso', 'Lumajang'];

        $counter = 1;
        $batches = [2022, 2023, 2024, 2025, 2026];

        foreach ($studyPrograms as $program) {
            foreach ($batches as $batch) {
                $jumlah = $batch === 2026 ? 5 : rand(7, 12);

                for ($i = 0; $i < $jumlah; $i++) {
                    $gender = $i % 2 === 0 ? 'L' : 'P';
                    $namaDepan = $gender === 'L'
                        ? $namaLaki[array_rand($namaLaki)]
                        : $namaPerempuan[array_rand($namaPerempuan)];
                    $namaBelakangPilih = $namaBelakang[array_rand($namaBelakang)];
                    $fullName = $gender === 'L'
                        ? "$namaDepan $namaBelakangPilih"
                        : "Siti $namaDepan $namaBelakangPilih";

                    $nim = $batch . $program->code . str_pad($counter, 4, '0', STR_PAD_LEFT);
                    $email = strtolower(str_replace(' ', '.', $fullName)) . '.' . $nim . '@student.iaimu.ac.id';
                    $semester = max(1, min(8, (2026 - $batch) * 2 + 1));

                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $nim,
                            'full_name' => $fullName,
                            'email' => $email,
                            'password' => Hash::make($nim),
                            'status' => 'active',
                        ]
                    );
                    $user->assignRole('mahasiswa');

                    $student = Student::firstOrCreate(
                        ['nim' => $nim],
                        [
                            'user_id' => $user->id,
                            'study_program_id' => $program->id,
                            'faculty_id' => $program->faculty_id,
                            'nim' => $nim,
                            'full_name' => $fullName,
                            'gender' => $gender,
                            'birth_place' => $kotaLahir[array_rand($kotaLahir)],
                            'birth_date' => (2003 - ($batch - 2020)) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                            'religion' => 'islam',
                            'address' => 'Jl. Contoh No. ' . rand(1, 100) . ', Pamekasan',
                            'phone' => '08' . rand(100000000, 999999999),
                            'email' => $email,
                            'batch' => $batch,
                            'status' => $batch === 2022 && $i < 3 ? 'lulus' : 'aktif',
                            'current_semester' => $semester,
                            'gpa' => round(rand(270, 400) / 100, 2),
                            'total_credits_passed' => max(0, ($semester - 1) * 20),
                        ]
                    );

                    // Tambahkan data orang tua
                    $student->guardian()->firstOrCreate(
                        ['student_id' => $student->id],
                        [
                            'father_name' => 'Bapak ' . $namaBelakangPilih,
                            'father_phone' => '08' . rand(100000000, 999999999),
                            'father_job' => ['Petani', 'Wiraswasta', 'PNS', 'Nelayan', 'Pedagang'][rand(0, 4)],
                            'mother_name' => 'Ibu ' . $namaPerempuan[array_rand($namaPerempuan)],
                            'mother_phone' => '08' . rand(100000000, 999999999),
                        ]
                    );

                    $counter++;
                }
            }
        }

        $this->command->info('✅ Mahasiswa dummy berhasil dibuat. Password = NIM masing-masing.');
    }
}
