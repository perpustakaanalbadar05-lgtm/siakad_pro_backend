<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StudentBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentBillingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StudentBilling::with(['student', 'academicYear', 'billingType']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $billings = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $billings->items(),
            'meta' => [
                'current_page' => $billings->currentPage(),
                'last_page' => $billings->lastPage(),
                'total' => $billings->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'billing_type_id' => 'required|exists:billing_types,id',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['invoice_number'] = 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(6));
        $validated['status'] = 'unpaid';

        $billing = StudentBilling::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat.',
            'data' => $billing,
        ], 201);
    }

    public function show(StudentBilling $studentBilling): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $studentBilling->load(['student', 'academicYear', 'billingType', 'payments.verifiedBy']),
        ]);
    }

    public function destroy(StudentBilling $studentBilling): JsonResponse
    {
        if ($studentBilling->status !== 'unpaid' && $studentBilling->payments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan yang sudah memiliki riwayat pembayaran tidak dapat dihapus.',
            ], 422);
        }

        $studentBilling->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dihapus.',
        ]);
    }
}
