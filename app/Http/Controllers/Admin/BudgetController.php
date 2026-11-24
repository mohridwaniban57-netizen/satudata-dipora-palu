<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Budget::with('sport');

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->input('sport_id'));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        $budgets = $query->orderBy('year', 'desc')->paginate(20);

        return response()->json($budgets);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sport_id' => 'required|integer|exists:sports,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 2),
            'amount' => 'required|numeric|min:0',
            'state' => 'required|in:draft,validated,published',
            'is_dummy' => 'boolean',
        ]);

        $budget = Budget::create($validated);

        return response()->json([
            'message' => 'Anggaran cabor berhasil disimpan.',
            'data' => $budget->load('sport'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $budget = Budget::with('sport')->findOrFail($id);

        return response()->json([
            'data' => $budget,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $budget = Budget::findOrFail($id);

        $validated = $request->validate([
            'sport_id' => 'sometimes|required|integer|exists:sports,id',
            'year' => 'sometimes|required|integer|min:2000|max:' . (date('Y') + 2),
            'amount' => 'sometimes|required|numeric|min:0',
            'state' => 'sometimes|required|in:draft,validated,published',
            'is_dummy' => 'boolean',
        ]);

        $budget->update($validated);

        return response()->json([
            'message' => 'Anggaran cabor berhasil diperbarui.',
            'data' => $budget->load('sport'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();

        return response()->json([
            'message' => 'Anggaran cabor berhasil dihapus.',
        ]);
    }
}
