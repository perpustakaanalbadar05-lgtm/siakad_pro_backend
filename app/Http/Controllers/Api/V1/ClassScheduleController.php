<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ClassSchedule::with(['academicYear', 'course', 'lecturer', 'room']);

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->has('lecturer_id')) {
            $query->where('lecturer_id', $request->lecturer_id);
        }

        $schedules = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $schedules->items(),
            'meta' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'total' => $schedules->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'room_id' => 'required|exists:rooms,id',
            'day_of_week' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'quota' => 'required|integer|min:1',
        ]);

        // Cek bentrok ruangan
        $roomConflict = ClassSchedule::where('academic_year_id', $validated['academic_year_id'])
            ->where('room_id', $validated['room_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })->exists();

        if ($roomConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan sudah terpakai pada jam tersebut.',
            ], 422);
        }

        // Cek bentrok dosen
        $lecturerConflict = ClassSchedule::where('academic_year_id', $validated['academic_year_id'])
            ->where('lecturer_id', $validated['lecturer_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })->exists();

        if ($lecturerConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen memiliki jadwal mengajar lain pada jam tersebut.',
            ], 422);
        }

        $schedule = ClassSchedule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal kuliah berhasil dibuat.',
            'data' => $schedule,
        ], 201);
    }

    public function show(ClassSchedule $classSchedule): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $classSchedule->load(['academicYear', 'course', 'lecturer', 'room']),
        ]);
    }

    public function destroy(ClassSchedule $classSchedule): JsonResponse
    {
        $classSchedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal kuliah berhasil dihapus.',
        ]);
    }
}
