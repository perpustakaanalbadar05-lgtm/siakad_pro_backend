<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicGrade;
use App\Models\ClassSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function getByStudent(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $grades = AcademicGrade::with('classSchedule.course')
            ->where('student_id', $request->student_id)
            ->whereHas('classSchedule', function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    public function updateGrades(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.presence_score' => 'numeric|min:0|max:100',
            'grades.*.assignment_score' => 'numeric|min:0|max:100',
            'grades.*.mid_exam_score' => 'numeric|min:0|max:100',
            'grades.*.final_exam_score' => 'numeric|min:0|max:100',
        ]);

        foreach ($validated['grades'] as $gradeData) {
            $finalNumber = ($gradeData['presence_score'] * 0.1) +
                           ($gradeData['assignment_score'] * 0.2) +
                           ($gradeData['mid_exam_score'] * 0.3) +
                           ($gradeData['final_exam_score'] * 0.4);

            $finalLetter = $this->calculateGradeLetter($finalNumber);
            $status = in_array($finalLetter, ['A', 'AB', 'B', 'BC', 'C']) ? 'passed' : 'failed';

            AcademicGrade::updateOrCreate(
                [
                    'class_schedule_id' => $validated['class_schedule_id'],
                    'student_id' => $gradeData['student_id'],
                ],
                [
                    'presence_score' => $gradeData['presence_score'],
                    'assignment_score' => $gradeData['assignment_score'],
                    'mid_exam_score' => $gradeData['mid_exam_score'],
                    'final_exam_score' => $gradeData['final_exam_score'],
                    'final_grade_number' => $finalNumber,
                    'final_grade_letter' => $finalLetter,
                    'status' => $status,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan.',
        ]);
    }

    private function calculateGradeLetter($number): string
    {
        if ($number >= 85) return 'A';
        if ($number >= 80) return 'AB';
        if ($number >= 75) return 'B';
        if ($number >= 70) return 'BC';
        if ($number >= 65) return 'C';
        if ($number >= 50) return 'D';
        return 'E';
    }
}
