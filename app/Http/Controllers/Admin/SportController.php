<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SportController extends Controller
{
    public function index(): JsonResponse
    {
        $sports = Sport::withCount(['athletes', 'medalHistories', 'budgets'])->orderBy('name', 'asc')->get();

        return response()->json([
            'data' => $sports,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sports,name',
        ]);

        $sport = Sport::create($validated);

        return response()->json([
            'message' => 'Cabang olahraga berhasil ditambahkan.',
            'data' => $sport,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $sport = Sport::with(['athletes', 'medalHistories', 'budgets'])->findOrFail($id);

        return response()->json([
            'data' => $sport,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $sport = Sport::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sports,name,' . $sport->id,
        ]);

        $sport->update($validated);

        return response()->json([
            'message' => 'Cabang olahraga berhasil diperbarui.',
            'data' => $sport,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $sport = Sport::findOrFail($id);
        $sport->delete();

        return response()->json([
            'message' => 'Cabang olahraga berhasil dihapus.',
        ]);
    }
}
