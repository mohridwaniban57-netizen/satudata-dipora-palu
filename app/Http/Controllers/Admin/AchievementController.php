<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Achievement::with('athlete');

        if ($request->filled('athlete_id')) {
            $query->where('athlete_id', $request->input('athlete_id'));
        }

        $achievements = $query->orderBy('year', 'desc')->paginate(20);

        return response()->json($achievements);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|integer|exists:athletes,id',
            'event' => 'required|string|max:255',
            'medal' => 'required|in:gold,silver,bronze',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $achievement = Achievement::create($validated);

        return response()->json([
            'message' => 'Prestasi atlet berhasil ditambahkan.',
            'data' => $achievement->load('athlete'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $achievement = Achievement::with('athlete')->findOrFail($id);

        return response()->json([
            'data' => $achievement,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);

        $validated = $request->validate([
            'athlete_id' => 'sometimes|required|integer|exists:athletes,id',
            'event' => 'sometimes|required|string|max:255',
            'medal' => 'sometimes|required|in:gold,silver,bronze',
            'year' => 'sometimes|required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $achievement->update($validated);

        return response()->json([
            'message' => 'Prestasi atlet berhasil diperbarui.',
            'data' => $achievement->load('athlete'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();

        return response()->json([
            'message' => 'Prestasi atlet berhasil dihapus.',
        ]);
    }
}
