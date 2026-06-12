<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Faculty::withCount(['studyPrograms', 'lecturers', 'students']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $faculties = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data fakultas berhasil diambil.',
            'data' => $faculties,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:faculties,code',
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:20',
            'dean_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['created_by'] = auth()->id();
        $faculty = Faculty::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil ditambahkan.',
            'data' => $faculty,
        ], 201);
    }

    public function show(Faculty $faculty): JsonResponse
    {
        $faculty->load(['studyPrograms', 'lecturers', 'students']);

        return response()->json([
            'success' => true,
            'message' => 'Data fakultas berhasil diambil.',
            'data' => $faculty,
        ]);
    }

    public function update(Request $request, Faculty $faculty): JsonResponse
    {
        $validated = $request->validate([
            'code' => "required|string|max:10|unique:faculties,code,{$faculty->id}",
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:20',
            'dean_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['updated_by'] = auth()->id();
        $faculty->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil diperbarui.',
            'data' => $faculty->fresh(),
        ]);
    }

    public function destroy(Faculty $faculty): JsonResponse
    {
        if ($faculty->studyPrograms()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Fakultas tidak dapat dihapus karena masih memiliki program studi.',
            ], 422);
        }

        $faculty->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil dihapus.',
        ]);
    }
}
