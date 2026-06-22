<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AcademicCalendar::with('academicYear');

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $calendars = $query->orderBy('start_date', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $calendars,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:perkuliahan,krs,uts,uas,libur,wisuda,lainnya',
            'description' => 'nullable|string',
        ]);

        $calendar = AcademicCalendar::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Agenda akademik berhasil ditambahkan.',
            'data' => $calendar,
        ], 201);
    }

    public function show(AcademicCalendar $academicCalendar): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $academicCalendar->load('academicYear'),
        ]);
    }

    public function update(Request $request, AcademicCalendar $academicCalendar): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:perkuliahan,krs,uts,uas,libur,wisuda,lainnya',
            'description' => 'nullable|string',
        ]);

        $academicCalendar->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Agenda akademik berhasil diperbarui.',
            'data' => $academicCalendar,
        ]);
    }

    public function destroy(AcademicCalendar $academicCalendar): JsonResponse
    {
        $academicCalendar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agenda akademik berhasil dihapus.',
        ]);
    }
}
