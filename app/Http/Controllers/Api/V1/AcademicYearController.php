<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AcademicYear::query();

        if ($request->has('search')) {
            $query->where('year', 'like', "%{$request->search}%")
                ->orWhere('label', 'like', "%{$request->search}%");
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $years = $query->orderByDesc('start_date')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data tahun akademik berhasil diambil.',
            'data' => $years,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'required|string|max:9',
            'semester' => 'required|in:ganjil,genap,pendek',
            'label' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'krs_start' => 'nullable|date',
            'krs_end' => 'nullable|date|after_or_equal:krs_start',
            'mid_exam_start' => 'nullable|date',
            'mid_exam_end' => 'nullable|date|after_or_equal:mid_exam_start',
            'final_exam_start' => 'nullable|date',
            'final_exam_end' => 'nullable|date|after_or_equal:final_exam_start',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        DB::transaction(function () use ($validated, &$academicYear) {
            if ($validated['is_active'] ?? false) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            $academicYear = AcademicYear::create($validated);
        });

        return response()->json([
            'success' => true,
            'message' => 'Tahun akademik berhasil ditambahkan.',
            'data' => $academicYear,
        ], 201);
    }

    public function show(AcademicYear $academicYear): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data tahun akademik berhasil diambil.',
            'data' => $academicYear,
        ]);
    }

    public function update(Request $request, AcademicYear $academicYear): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'required|string|max:9',
            'semester' => 'required|in:ganjil,genap,pendek',
            'label' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'krs_start' => 'nullable|date',
            'krs_end' => 'nullable|date|after_or_equal:krs_start',
            'mid_exam_start' => 'nullable|date',
            'mid_exam_end' => 'nullable|date|after_or_equal:mid_exam_start',
            'final_exam_start' => 'nullable|date',
            'final_exam_end' => 'nullable|date|after_or_equal:final_exam_start',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();

        DB::transaction(function () use ($validated, $academicYear) {
            if ($validated['is_active'] ?? false) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            $academicYear->update($validated);
        });

        return response()->json([
            'success' => true,
            'message' => 'Tahun akademik berhasil diperbarui.',
            'data' => $academicYear->fresh(),
        ]);
    }

    public function destroy(AcademicYear $academicYear): JsonResponse
    {
        if ($academicYear->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun akademik aktif tidak dapat dihapus.',
            ], 422);
        }

        $academicYear->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tahun akademik berhasil dihapus.',
        ]);
    }

    public function activate(AcademicYear $academicYear): JsonResponse
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_active' => false]);
            $academicYear->update(['is_active' => true, 'updated_by' => auth()->id()]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Tahun akademik berhasil diaktifkan.',
            'data' => $academicYear->fresh(),
        ]);
    }
}
