<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Curriculum::with('studyProgram');

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }

        $curriculums = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kurikulum berhasil diambil.',
            'data' => $curriculums,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:9',
            'is_active' => 'boolean',
        ]);

        $curriculum = Curriculum::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil ditambahkan.',
            'data' => $curriculum,
        ], 201);
    }

    public function show(Curriculum $curriculum): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data kurikulum berhasil diambil.',
            'data' => $curriculum->load('studyProgram'),
        ]);
    }

    public function update(Request $request, Curriculum $curriculum): JsonResponse
    {
        $validated = $request->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:9',
            'is_active' => 'boolean',
        ]);

        $curriculum->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil diperbarui.',
            'data' => $curriculum->fresh('studyProgram'),
        ]);
    }

    public function destroy(Curriculum $curriculum): JsonResponse
    {
        $curriculum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil dihapus.',
        ]);
    }
}
