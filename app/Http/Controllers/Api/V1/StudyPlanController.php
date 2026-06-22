<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\ClassSchedule;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudyPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StudyPlan::with(['student', 'academicYear']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $studyPlans = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $studyPlans,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_schedule_ids' => 'required|array',
            'class_schedule_ids.*' => 'exists:class_schedules,id',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear || $activeYear->id != $validated['academic_year_id']) {
            return response()->json(['success' => false, 'message' => 'Tahun akademik tidak aktif.'], 422);
        }

        DB::beginTransaction();
        try {
            $studyPlan = StudyPlan::updateOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                ],
                [
                    'status' => 'pending_approval',
                    'total_credits' => 0, // Akan dihitung nanti
                ]
            );

            // Hapus detail lama jika ada
            $studyPlan->details()->delete();

            $totalCredits = 0;
            foreach ($validated['class_schedule_ids'] as $scheduleId) {
                $schedule = ClassSchedule::with('course')->find($scheduleId);
                
                StudyPlanDetail::create([
                    'study_plan_id' => $studyPlan->id,
                    'class_schedule_id' => $schedule->id,
                ]);

                $totalCredits += $schedule->course->credits;
            }

            $studyPlan->update(['total_credits' => $totalCredits]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'KRS berhasil diajukan dan menunggu persetujuan dosen wali.',
                'data' => $studyPlan->load('details.classSchedule.course'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan KRS: ' . $e->getMessage()], 500);
        }
    }

    public function approve(StudyPlan $studyPlan): JsonResponse
    {
        $studyPlan->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil disetujui.',
            'data' => $studyPlan,
        ]);
    }

    public function show(StudyPlan $studyPlan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $studyPlan->load(['student', 'academicYear', 'details.classSchedule.course', 'details.classSchedule.lecturer', 'details.classSchedule.room']),
        ]);
    }
}
