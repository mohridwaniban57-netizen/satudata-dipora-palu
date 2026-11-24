<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VenueRevenue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VenueRevenue::with('venue');

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->input('venue_id'));
        }

        $revenues = $query->orderBy('month', 'desc')->paginate(20);

        return response()->json($revenues);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => 'required|integer|exists:venues,id',
            'month' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ]);

        $revenue = VenueRevenue::updateOrCreate(
            ['venue_id' => $validated['venue_id'], 'month' => $validated['month']],
            ['amount' => $validated['amount']]
        );

        return response()->json([
            'message' => 'Data retribusi berhasil disimpan.',
            'data' => $revenue->load('venue'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $revenue = VenueRevenue::with('venue')->findOrFail($id);

        return response()->json([
            'data' => $revenue,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $revenue = VenueRevenue::findOrFail($id);

        $validated = $request->validate([
            'venue_id' => 'sometimes|required|integer|exists:venues,id',
            'month' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0',
        ]);

        $revenue->update($validated);

        return response()->json([
            'message' => 'Data retribusi berhasil diperbarui.',
            'data' => $revenue->load('venue'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $revenue = VenueRevenue::findOrFail($id);
        $revenue->delete();

        return response()->json([
            'message' => 'Data retribusi berhasil dihapus.',
        ]);
    }
}
