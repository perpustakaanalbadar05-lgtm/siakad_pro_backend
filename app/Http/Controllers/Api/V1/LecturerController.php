<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Lecturer::with(['faculty', 'studyProgram']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                    ->orWhere('nidn', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 20);
        $lecturers = $query->orderBy('full_name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil diambil.',
            'data' => $lecturers->items(),
            'meta' => [
                'current_page' => $lecturers->currentPage(),
                'last_page' => $lecturers->lastPage(),
                'per_page' => $lecturers->perPage(),
                'total' => $lecturers->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'study_program_id' => 'required|exists:study_programs,id',
            'nidn' => 'nullable|string|max:20|unique:lecturers,nidn',
            'nidk' => 'nullable|string|max:20|unique:lecturers,nidk',
            'nip' => 'nullable|string|max:30',
            'full_name' => 'required|string|max:255',
            'front_title' => 'nullable|string|max:50',
            'back_title' => 'nullable|string|max:100',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'employment_status' => 'nullable|in:tetap,luar_biasa,kontrak',
            'status' => 'nullable|in:active,cuti,tugas_belajar,pensiun,inactive',
        ]);

        DB::transaction(function () use ($validated, &$lecturer) {
            $userId = null;
            if (!empty($validated['email'])) {
                $username = $validated['nidn'] ?? strtolower(str_replace(' ', '.', $validated['full_name']));
                $user = User::create([
                    'name' => $username,
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($username),
                    'status' => 'active',
                ]);
                $user->assignRole('dosen');
                $userId = $user->id;
            }

            $lecturer = Lecturer::create([
                ...$validated,
                'user_id' => $userId,
                'status' => $validated['status'] ?? 'active',
                'created_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil ditambahkan.',
            'data' => $lecturer->load(['faculty', 'studyProgram']),
        ], 201);
    }

    public function show(Lecturer $lecturer): JsonResponse
    {
        $lecturer->load(['faculty', 'studyProgram', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil diambil.',
            'data' => $lecturer,
        ]);
    }

    public function update(Request $request, Lecturer $lecturer): JsonResponse
    {
        $validated = $request->validate([
            'faculty_id' => 'sometimes|exists:faculties,id',
            'study_program_id' => 'sometimes|exists:study_programs,id',
            'nidn' => "nullable|string|max:20|unique:lecturers,nidn,{$lecturer->id}",
            'nidk' => "nullable|string|max:20|unique:lecturers,nidk,{$lecturer->id}",
            'nip' => 'nullable|string|max:30',
            'full_name' => 'sometimes|string|max:255',
            'front_title' => 'nullable|string|max:50',
            'back_title' => 'nullable|string|max:100',
            'gender' => 'sometimes|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'employment_status' => 'nullable|in:tetap,luar_biasa,kontrak',
            'status' => 'nullable|in:active,cuti,tugas_belajar,pensiun,inactive',
        ]);

        $validated['updated_by'] = auth()->id();
        $lecturer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil diperbarui.',
            'data' => $lecturer->fresh(['faculty', 'studyProgram']),
        ]);
    }

    public function destroy(Lecturer $lecturer): JsonResponse
    {
        $lecturer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data dosen berhasil dihapus.',
        ]);
    }
}
