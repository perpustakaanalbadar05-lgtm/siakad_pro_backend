<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['studyProgram.faculty', 'faculty']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                    ->orWhere('nim', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }

        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('batch')) {
            $query->where('batch', $request->batch);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 20);
        $students = $query->orderBy('nim')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diambil.',
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'faculty_id' => 'required|exists:faculties,id',
            'nim' => 'required|string|max:20|unique:students,nim',
            'nik' => 'nullable|string|max:20',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|in:islam,kristen,katolik,hindu,buddha,konghucu',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'batch' => 'required|integer|min:2000|max:2099',
            'status' => 'nullable|in:aktif,cuti,nonaktif,lulus,drop_out,mengundurkan_diri',
            // Guardian data
            'guardian.father_name' => 'nullable|string|max:255',
            'guardian.father_phone' => 'nullable|string|max:20',
            'guardian.father_job' => 'nullable|string|max:100',
            'guardian.mother_name' => 'nullable|string|max:255',
            'guardian.mother_phone' => 'nullable|string|max:20',
            'guardian.guardian_name' => 'nullable|string|max:255',
            'guardian.guardian_phone' => 'nullable|string|max:20',
            'guardian.guardian_relation' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $request, &$student) {
            // Buat user account jika email diisi
            $userId = null;
            if (!empty($validated['email'])) {
                $user = User::create([
                    'name' => $validated['nim'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['nim']), // Default password = NIM
                    'status' => 'active',
                ]);
                $user->assignRole('mahasiswa');
                $userId = $user->id;
            }

            $student = Student::create([
                ...$validated,
                'user_id' => $userId,
                'status' => $validated['status'] ?? 'aktif',
                'current_semester' => 1,
                'created_by' => auth()->id(),
            ]);

            // Buat data wali jika ada
            if ($request->has('guardian')) {
                $student->guardian()->create($request->input('guardian', []));
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan.',
            'data' => $student->load(['studyProgram.faculty', 'guardian']),
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['studyProgram.faculty', 'faculty', 'guardian', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diambil.',
            'data' => $student,
        ]);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'study_program_id' => 'sometimes|exists:study_programs,id',
            'faculty_id' => 'sometimes|exists:faculties,id',
            'nim' => "sometimes|string|max:20|unique:students,nim,{$student->id}",
            'nik' => 'nullable|string|max:20',
            'full_name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|in:islam,kristen,katolik,hindu,buddha,konghucu',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'batch' => 'sometimes|integer|min:2000|max:2099',
            'status' => 'nullable|in:aktif,cuti,nonaktif,lulus,drop_out,mengundurkan_diri',
            'guardian.father_name' => 'nullable|string|max:255',
            'guardian.father_phone' => 'nullable|string|max:20',
            'guardian.mother_name' => 'nullable|string|max:255',
            'guardian.mother_phone' => 'nullable|string|max:20',
            'guardian.guardian_name' => 'nullable|string|max:255',
            'guardian.guardian_phone' => 'nullable|string|max:20',
        ]);

        $validated['updated_by'] = auth()->id();

        DB::transaction(function () use ($validated, $request, $student) {
            $student->update($validated);

            if ($request->has('guardian')) {
                $student->guardian()->updateOrCreate(
                    ['student_id' => $student->id],
                    $request->input('guardian', [])
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui.',
            'data' => $student->fresh(['studyProgram.faculty', 'guardian']),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus.',
        ]);
    }
}
