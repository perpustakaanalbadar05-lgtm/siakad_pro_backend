<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\Curriculum;
use App\Models\Course;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // FAKULTAS
        // =====================================================================
        $faculties = [
            [
                'code' => 'FD',
                'name' => 'Fakultas Dakwah',
                'short_name' => 'DAKWAH',
                'status' => 'active',
                'programs' => [
                    ['code' => 'BPI', 'name' => 'Bimbingan dan Penyuluhan Islam', 'short_name' => 'BPI'],
                    ['code' => 'MD', 'name' => 'Manajemen Dakwah', 'short_name' => 'MD'],
                ],
            ],
            [
                'code' => 'FS',
                'name' => 'Fakultas Syariah',
                'short_name' => 'SYARIAH',
                'status' => 'active',
                'programs' => [
                    ['code' => 'HKI', 'name' => 'Hukum Keluarga Islam', 'short_name' => 'HKI'],
                    ['code' => 'ES', 'name' => 'Ekonomi Syariah', 'short_name' => 'ES'],
                ],
            ],
            [
                'code' => 'FT',
                'name' => 'Fakultas Tarbiyah',
                'short_name' => 'TARBIYAH',
                'status' => 'active',
                'programs' => [
                    ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam', 'short_name' => 'PAI'],
                    ['code' => 'PBA', 'name' => 'Pendidikan Bahasa Arab', 'short_name' => 'PBA'],
                ],
            ],
        ];

        foreach ($faculties as $facultyData) {
            $programs = $facultyData['programs'];
            unset($facultyData['programs']);

            $faculty = Faculty::firstOrCreate(['code' => $facultyData['code']], $facultyData);

            foreach ($programs as $programData) {
                $program = StudyProgram::firstOrCreate(
                    ['code' => $programData['code']],
                    array_merge($programData, [
                        'faculty_id' => $faculty->id,
                        'level' => 'S1',
                        'status' => 'active',
                    ])
                );

                // Buat kurikulum aktif untuk setiap prodi
                $curriculum = Curriculum::firstOrCreate(
                    ['study_program_id' => $program->id, 'year' => 2023],
                    [
                        'study_program_id' => $program->id,
                        'name' => "Kurikulum {$program->short_name} 2023",
                        'year' => 2023,
                        'is_active' => true,
                    ]
                );

                // Buat contoh mata kuliah
                $this->createSampleCourses($curriculum, $program);
            }
        }

        // =====================================================================
        // GEDUNG & RUANGAN
        // =====================================================================
        $building = Building::firstOrCreate(
            ['code' => 'GU'],
            ['code' => 'GU', 'name' => 'Gedung Utama', 'location' => 'Kampus IAIMU Pamekasan', 'status' => 'active']
        );

        $rooms = [
            ['code' => 'GU-101', 'name' => 'Ruang Kuliah 101', 'capacity' => 40],
            ['code' => 'GU-102', 'name' => 'Ruang Kuliah 102', 'capacity' => 40],
            ['code' => 'GU-103', 'name' => 'Ruang Kuliah 103', 'capacity' => 35],
            ['code' => 'GU-201', 'name' => 'Ruang Kuliah 201', 'capacity' => 40],
            ['code' => 'GU-202', 'name' => 'Ruang Kuliah 202', 'capacity' => 40],
            ['code' => 'GU-LAB', 'name' => 'Laboratorium Komputer', 'capacity' => 30, 'type' => 'laboratorium'],
            ['code' => 'GU-AUL', 'name' => 'Aula Utama', 'capacity' => 300, 'type' => 'aula'],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['code' => $room['code']],
                array_merge($room, ['building_id' => $building->id, 'status' => 'active', 'type' => $room['type'] ?? 'kelas'])
            );
        }

        $this->command->info('✅ Fakultas, program studi, kurikulum, gedung, dan ruangan berhasil dibuat.');
    }

    private function createSampleCourses(Curriculum $curriculum, StudyProgram $program): void
    {
        $mataKuliah = [
            ['code' => "{$program->code}-101", 'name' => 'Studi Al-Quran', 'credits' => 2, 'semester' => 1, 'type' => 'wajib'],
            ['code' => "{$program->code}-102", 'name' => 'Studi Al-Hadits', 'credits' => 2, 'semester' => 1, 'type' => 'wajib'],
            ['code' => "{$program->code}-103", 'name' => 'Bahasa Indonesia', 'credits' => 2, 'semester' => 1, 'type' => 'wajib'],
            ['code' => "{$program->code}-104", 'name' => 'Bahasa Inggris', 'credits' => 2, 'semester' => 1, 'type' => 'wajib'],
            ['code' => "{$program->code}-105", 'name' => 'Pancasila dan Kewarganegaraan', 'credits' => 2, 'semester' => 1, 'type' => 'wajib'],
            ['code' => "{$program->code}-201", 'name' => 'Ilmu Dakwah', 'credits' => 3, 'semester' => 2, 'type' => 'wajib'],
            ['code' => "{$program->code}-202", 'name' => 'Metodologi Penelitian', 'credits' => 3, 'semester' => 4, 'type' => 'wajib'],
            ['code' => "{$program->code}-301", 'name' => 'Skripsi', 'credits' => 6, 'semester' => 8, 'type' => 'wajib'],
        ];

        foreach ($mataKuliah as $mk) {
            Course::firstOrCreate(
                ['code' => $mk['code']],
                array_merge($mk, [
                    'curriculum_id' => $curriculum->id,
                    'study_program_id' => $program->id,
                    'status' => 'active',
                ])
            );
        }
    }
}
