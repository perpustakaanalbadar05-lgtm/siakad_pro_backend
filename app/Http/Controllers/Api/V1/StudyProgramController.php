<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudyProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StudyProgram::with('faculty')->withCount(['lecturers', 'students']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $programs = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data program studi berhasil diambil.',
            'data' => $programs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|string|max:10|unique:study_programs,code',
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:20',
            'level' => 'nullable|in:D3,S1,S2,S3',
            'accreditation' => 'nullable|string|max:5',
            'head_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['created_by'] = auth()->id();
        $program = StudyProgram::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program studi berhasil ditambahkan.',
            'data' => $program->load('faculty'),
        ], 201);
    }

    public function show(StudyProgram $studyProgram): JsonResponse
    {
        $studyProgram->load(['faculty', 'curriculums', 'lecturers', 'students']);

        return response()->json([
            'success' => true,
            'message' => 'Data program studi berhasil diambil.',
            'data' => $studyProgram,
        ]);
    }

    public function update(Request $request, StudyProgram $studyProgram): JsonResponse
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => "required|string|max:10|unique:study_programs,code,{$studyProgram->id}",
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:20',
            'level' => 'nullable|in:D3,S1,S2,S3',
            'accreditation' => 'nullable|string|max:5',
            'head_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['updated_by'] = auth()->id();
        $studyProgram->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program studi berhasil diperbarui.',
            'data' => $studyProgram->fresh(['faculty']),
        ]);
    }

    public function destroy(StudyProgram $studyProgram): JsonResponse
    {
        if ($studyProgram->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Program studi tidak dapat dihapus karena masih memiliki mahasiswa.',
            ], 422);
        }

        $studyProgram->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program studi berhasil dihapus.',
        ]);
    }
}
