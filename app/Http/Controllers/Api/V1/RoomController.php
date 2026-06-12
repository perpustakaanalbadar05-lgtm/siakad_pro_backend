<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Room::with('building');

        if ($request->has('search')) {
            $query->where('code', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%");
        }

        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        $rooms = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data ruangan berhasil diambil.',
            'data' => $rooms,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'code' => 'required|string|max:20|unique:rooms',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:classroom,laboratory,office,other',
            'floor' => 'nullable|integer',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil ditambahkan.',
            'data' => $room,
        ], 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data ruangan berhasil diambil.',
            'data' => $room->load('building'),
        ]);
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'code' => 'required|string|max:20|unique:rooms,code,' . $room->id,
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:classroom,laboratory,office,other',
            'floor' => 'nullable|integer',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil diperbarui.',
            'data' => $room->fresh('building'),
        ]);
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil dihapus.',
        ]);
    }
}
