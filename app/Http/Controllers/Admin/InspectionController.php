<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Inspection::with('venue');

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->input('venue_id'));
        }

        $inspections = $query->orderBy('inspected_on', 'desc')->paginate(20);

        return response()->json($inspections);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => 'required|integer|exists:venues,id',
            'inspected_on' => 'required|date',
            'score' => 'required|integer|between:0,100',
            'notes' => 'nullable|string',
        ]);

        $inspection = Inspection::create($validated);

        return response()->json([
            'message' => 'Data inspeksi berhasil disimpan.',
            'data' => $inspection->load('venue'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $inspection = Inspection::with('venue')->findOrFail($id);

        return response()->json([
            'data' => $inspection,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);

        $validated = $request->validate([
            'venue_id' => 'sometimes|required|integer|exists:venues,id',
            'inspected_on' => 'sometimes|required|date',
            'score' => 'sometimes|required|integer|between:0,100',
            'notes' => 'nullable|string',
        ]);

        $inspection->update($validated);

        return response()->json([
            'message' => 'Data inspeksi berhasil diperbarui.',
            'data' => $inspection->load('venue'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->delete();

        return response()->json([
            'message' => 'Data inspeksi berhasil dihapus.',
        ]);
    }
}
