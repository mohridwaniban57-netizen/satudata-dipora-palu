<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VenueUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VenueUsage::with('venue');

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->input('venue_id'));
        }

        $usages = $query->orderBy('month', 'desc')->paginate(20);

        return response()->json($usages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => 'required|integer|exists:venues,id',
            'month' => 'required|date',
            'visits' => 'required|integer|min:0',
        ]);

        $usage = VenueUsage::updateOrCreate(
            ['venue_id' => $validated['venue_id'], 'month' => $validated['month']],
            ['visits' => $validated['visits']]
        );

        return response()->json([
            'message' => 'Data penggunaan venue berhasil disimpan.',
            'data' => $usage->load('venue'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $usage = VenueUsage::with('venue')->findOrFail($id);

        return response()->json([
            'data' => $usage,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $usage = VenueUsage::findOrFail($id);

        $validated = $request->validate([
            'venue_id' => 'sometimes|required|integer|exists:venues,id',
            'month' => 'sometimes|required|date',
            'visits' => 'sometimes|required|integer|min:0',
        ]);

        $usage->update($validated);

        return response()->json([
            'message' => 'Data penggunaan venue berhasil diperbarui.',
            'data' => $usage->load('venue'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $usage = VenueUsage::findOrFail($id);
        $usage->delete();

        return response()->json([
            'message' => 'Data penggunaan venue berhasil dihapus.',
        ]);
    }
}
