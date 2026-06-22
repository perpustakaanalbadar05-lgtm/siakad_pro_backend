<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BillingType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $billingTypes = BillingType::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $billingTypes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'default_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $billingType = BillingType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jenis tagihan berhasil ditambahkan.',
            'data' => $billingType,
        ], 201);
    }

    public function show(BillingType $billingType): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $billingType,
        ]);
    }

    public function update(Request $request, BillingType $billingType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'default_amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $billingType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jenis tagihan berhasil diperbarui.',
            'data' => $billingType,
        ]);
    }

    public function destroy(BillingType $billingType): JsonResponse
    {
        $billingType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis tagihan berhasil dihapus.',
        ]);
    }
}
