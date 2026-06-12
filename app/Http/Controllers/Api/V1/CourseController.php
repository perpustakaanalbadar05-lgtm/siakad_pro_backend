<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['studyProgram', 'curriculum']);

        if ($request->has('search')) {
            $query->where('code', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%");
        }

        if ($request->has('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        $courses = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'message' => 'Data mata kuliah berhasil diambil.',
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'study_program_id' => 'required|exists:study_programs,id',
            'code' => 'required|string|max:20|unique:courses',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'theory_credits' => 'required|integer|min:0|max:6',
            'practice_credits' => 'required|integer|min:0|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'type' => 'required|in:wajib,pilihan',
            'is_active' => 'boolean',
        ]);

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data' => $course,
        ], 201);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data mata kuliah berhasil diambil.',
            'data' => $course->load(['studyProgram', 'curriculum']),
        ]);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'study_program_id' => 'required|exists:study_programs,id',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'theory_credits' => 'required|integer|min:0|max:6',
            'practice_credits' => 'required|integer|min:0|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'type' => 'required|in:wajib,pilihan',
            'is_active' => 'boolean',
        ]);

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data' => $course->fresh(['studyProgram', 'curriculum']),
        ]);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus.',
        ]);
    }
}
