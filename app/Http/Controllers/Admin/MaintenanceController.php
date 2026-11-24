<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MaintenanceRecord::with('venue');

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->input('venue_id'));
        }

        if ($request->has('is_incident')) {
            $query->where('is_incident', $request->boolean('is_incident'));
        }

        $records = $query->orderBy('done_on', 'desc')->paginate(20);

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => 'required|integer|exists:venues,id',
            'done_on' => 'required|date',
            'type' => 'required|string|max:100',
            'cost' => 'required|numeric|min:0',
            'is_incident' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $record = MaintenanceRecord::create($validated);

        return response()->json([
            'message' => 'Catatan pemeliharaan berhasil disimpan.',
            'data' => $record->load('venue'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $record = MaintenanceRecord::with('venue')->findOrFail($id);

        return response()->json([
            'data' => $record,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $record = MaintenanceRecord::findOrFail($id);

        $validated = $request->validate([
            'venue_id' => 'sometimes|required|integer|exists:venues,id',
            'done_on' => 'sometimes|required|date',
            'type' => 'sometimes|required|string|max:100',
            'cost' => 'sometimes|required|numeric|min:0',
            'is_incident' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $record->update($validated);

        return response()->json([
            'message' => 'Catatan pemeliharaan berhasil diperbarui.',
            'data' => $record->load('venue'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $record = MaintenanceRecord::findOrFail($id);
        $record->delete();

        return response()->json([
            'message' => 'Catatan pemeliharaan berhasil dihapus.',
        ]);
    }
}
