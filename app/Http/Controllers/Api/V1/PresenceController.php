<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\PresenceDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresenceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'date' => 'required|date',
            'meeting_number' => 'required|integer|min:1|max:16',
            'material_description' => 'nullable|string',
            'students' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.status' => 'required|in:hadir,sakit,izin,alfa',
        ]);

        DB::beginTransaction();
        try {
            $presence = Presence::updateOrCreate(
                [
                    'class_schedule_id' => $validated['class_schedule_id'],
                    'meeting_number' => $validated['meeting_number'],
                ],
                [
                    'date' => $validated['date'],
                    'material_description' => $validated['material_description'],
                ]
            );

            foreach ($validated['students'] as $studentData) {
                PresenceDetail::updateOrCreate(
                    [
                        'presence_id' => $presence->id,
                        'student_id' => $studentData['student_id'],
                    ],
                    [
                        'status' => $studentData['status'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi pertemuan ke-'.$validated['meeting_number'].' berhasil disimpan.',
                'data' => $presence->load('details'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan presensi.'], 500);
        }
    }
}
