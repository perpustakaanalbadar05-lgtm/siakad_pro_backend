<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil.',
            'data' => [
                'stats' => [
                    'total_students' => Student::count(),
                    'active_students' => Student::active()->count(),
                    'total_lecturers' => Lecturer::count(),
                    'active_lecturers' => Lecturer::active()->count(),
                    'total_faculties' => Faculty::count(),
                    'total_study_programs' => StudyProgram::count(),
                ],
                'students_by_program' => StudyProgram::withCount('students')
                    ->with('faculty')
                    ->orderByDesc('students_count')
                    ->get()
                    ->map(fn($p) => [
                        'name' => $p->name,
                        'faculty' => $p->faculty->short_name ?? $p->faculty->name,
                        'count' => $p->students_count,
                    ]),
                'students_by_batch' => Student::selectRaw('batch, count(*) as total')
                    ->groupBy('batch')
                    ->orderByDesc('batch')
                    ->limit(5)
                    ->get(),
                'students_by_status' => Student::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->get(),
            ],
        ]);
    }

    public function student(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student?->load(['studyProgram.faculty']);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard mahasiswa berhasil diambil.',
            'data' => [
                'student' => $student,
                'stats' => [
                    'semester' => $student?->current_semester ?? 1,
                    'gpa' => $student?->gpa ?? 0,
                    'total_credits_passed' => $student?->total_credits_passed ?? 0,
                ],
            ],
        ]);
    }

    public function lecturer(Request $request): JsonResponse
    {
        $user = $request->user();
        $lecturer = $user->lecturer?->load(['faculty', 'studyProgram']);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard dosen berhasil diambil.',
            'data' => [
                'lecturer' => $lecturer,
            ],
        ]);
    }
}
