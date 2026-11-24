<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedalHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MedalHistory::with('sport');

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->input('sport_id'));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('event')) {
            $query->where('event', 'like', "%{$request->input('event')}%");
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        $medals = $query->orderBy('year', 'desc')->paginate(20);

        return response()->json($medals);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sport_id' => 'required|integer|exists:sports,id',
            'event' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 2),
            'gold' => 'required|integer|min:0',
            'silver' => 'required|integer|min:0',
            'bronze' => 'required|integer|min:0',
            'state' => 'required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $medal = MedalHistory::create($validated);

        return response()->json([
            'message' => 'Catatan medali berhasil ditambahkan.',
            'data' => $medal->load('sport'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $medal = MedalHistory::with('sport')->findOrFail($id);

        return response()->json([
            'data' => $medal,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $medal = MedalHistory::findOrFail($id);

        $validated = $request->validate([
            'sport_id' => 'sometimes|required|integer|exists:sports,id',
            'event' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer|min:2000|max:' . (date('Y') + 2),
            'gold' => 'sometimes|required|integer|min:0',
            'silver' => 'sometimes|required|integer|min:0',
            'bronze' => 'sometimes|required|integer|min:0',
            'state' => 'sometimes|required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $medal->update($validated);

        return response()->json([
            'message' => 'Catatan medali berhasil diperbarui.',
            'data' => $medal->load('sport'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $medal = MedalHistory::findOrFail($id);
        $medal->delete();

        return response()->json([
            'message' => 'Catatan medali berhasil dihapus.',
        ]);
    }
}
