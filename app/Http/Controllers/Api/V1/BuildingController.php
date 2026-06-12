<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Building::query();

        if ($request->has('search')) {
            $query->where('code', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%");
        }

        $buildings = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data gedung berhasil diambil.',
            'data' => $buildings,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:buildings',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $building = Building::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gedung berhasil ditambahkan.',
            'data' => $building,
        ], 201);
    }

    public function show(Building $building): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data gedung berhasil diambil.',
            'data' => $building,
        ]);
    }

    public function update(Request $request, Building $building): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:buildings,code,' . $building->id,
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $building->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gedung berhasil diperbarui.',
            'data' => $building->fresh(),
        ]);
    }

    public function destroy(Building $building): JsonResponse
    {
        $building->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gedung berhasil dihapus.',
        ]);
    }
}
