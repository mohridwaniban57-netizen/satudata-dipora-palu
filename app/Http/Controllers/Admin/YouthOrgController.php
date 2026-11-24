<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YouthOrg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class YouthOrgController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = YouthOrg::with('district');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        if ($request->filled('activity_level')) {
            $query->where('activity_level', $request->input('activity_level'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        $orgs = $query->orderBy('name', 'asc')->paginate(20);

        return response()->json($orgs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:OKP,Karang Taruna',
            'district_id' => 'required|integer|exists:districts,id',
            'members' => 'required|integer|min:0',
            'activity_level' => 'required|in:low,medium,high',
            'state' => 'required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $org = YouthOrg::create($validated);

        return response()->json([
            'message' => 'Organisasi kepemudaan berhasil ditambahkan.',
            'data' => $org->load('district'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $org = YouthOrg::with('district')->findOrFail($id);

        return response()->json([
            'data' => $org,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $org = YouthOrg::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:OKP,Karang Taruna',
            'district_id' => 'sometimes|required|integer|exists:districts,id',
            'members' => 'sometimes|required|integer|min:0',
            'activity_level' => 'sometimes|required|in:low,medium,high',
            'state' => 'sometimes|required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $org->update($validated);

        return response()->json([
            'message' => 'Organisasi kepemudaan berhasil diperbarui.',
            'data' => $org->load('district'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $org = YouthOrg::findOrFail($id);
        $org->delete();

        return response()->json([
            'message' => 'Organisasi kepemudaan berhasil dihapus.',
        ]);
    }
}
