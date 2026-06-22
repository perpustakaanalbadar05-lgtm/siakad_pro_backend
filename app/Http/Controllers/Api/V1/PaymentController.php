<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['studentBilling.student', 'studentBilling.billingType', 'verifiedBy']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_billing_id' => 'required|exists:student_billings,id',
            'amount_paid' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:manual,bank_transfer',
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $billing = StudentBilling::findOrFail($validated['student_billing_id']);

        // Hitung total bayar sebelumnya yang sudah verified
        $totalPaid = $billing->payments()->where('status', 'verified')->sum('amount_paid');
        $remaining = $billing->amount - $totalPaid;

        if ($validated['amount_paid'] > $remaining) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pembayaran melebihi sisa tagihan.',
            ], 422);
        }

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('proofs', 'public');
            $validated['proof_of_payment'] = $path;
        }

        $validated['status'] = 'pending';

        $payment = Payment::create([
            'student_billing_id' => $validated['student_billing_id'],
            'amount_paid' => $validated['amount_paid'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'proof_of_payment' => $validated['proof_of_payment'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi.',
            'data' => $payment,
        ], 201);
    }

    public function verify(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|nullable',
        ]);

        if ($payment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pembayaran sudah diverifikasi.'], 422);
        }

        DB::beginTransaction();
        try {
            if ($validated['action'] === 'approve') {
                $payment->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Update status tagihan
                $billing = $payment->studentBilling;
                $totalPaid = $billing->payments()->where('status', 'verified')->sum('amount_paid');
                
                if ($totalPaid >= $billing->amount) {
                    $billing->update(['status' => 'paid']);
                } else {
                    $billing->update(['status' => 'partial']);
                }

                $message = 'Pembayaran disetujui.';
            } else {
                $payment->update([
                    'status' => 'rejected',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                    'rejection_reason' => $validated['rejection_reason'],
                ]);
                $message = 'Pembayaran ditolak.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memverifikasi pembayaran.'], 500);
        }
    }
}
