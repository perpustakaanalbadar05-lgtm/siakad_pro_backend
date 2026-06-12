<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            [
                'year' => '2024/2025',
                'semester' => 'genap',
                'label' => 'Genap 2024/2025',
                'is_active' => false,
                'start_date' => '2025-02-01',
                'end_date' => '2025-07-31',
            ],
            [
                'year' => '2025/2026',
                'semester' => 'ganjil',
                'label' => 'Ganjil 2025/2026',
                'is_active' => false,
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
                'krs_start' => '2025-08-15',
                'krs_end' => '2025-09-07',
                'mid_exam_start' => '2025-11-03',
                'mid_exam_end' => '2025-11-14',
                'final_exam_start' => '2026-01-05',
                'final_exam_end' => '2026-01-16',
            ],
            [
                'year' => '2025/2026',
                'semester' => 'genap',
                'label' => 'Genap 2025/2026',
                'is_active' => false,
                'start_date' => '2026-02-01',
                'end_date' => '2026-07-31',
            ],
            [
                'year' => '2026/2027',
                'semester' => 'ganjil',
                'label' => 'Ganjil 2026/2027',
                'is_active' => true,
                'start_date' => '2026-09-01',
                'end_date' => '2027-01-31',
                'krs_start' => '2026-08-15',
                'krs_end' => '2026-09-07',
                'mid_exam_start' => '2026-11-02',
                'mid_exam_end' => '2026-11-13',
                'final_exam_start' => '2027-01-04',
                'final_exam_end' => '2027-01-15',
            ],
        ];

        foreach ($years as $year) {
            AcademicYear::firstOrCreate(
                ['year' => $year['year'], 'semester' => $year['semester']],
                $year
            );
        }

        $this->command->info('✅ Tahun akademik berhasil dibuat (aktif: Ganjil 2026/2027).');
    }
}
