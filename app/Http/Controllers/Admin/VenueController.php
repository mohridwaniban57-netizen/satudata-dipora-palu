<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Venue::with(['venueType', 'district']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        if ($request->filled('venue_type_id')) {
            $query->where('venue_type_id', $request->input('venue_type_id'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        $venues = $query->orderBy('name', 'asc')->paginate(20);

        return response()->json($venues);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'venue_type_id' => 'required|integer|exists:venue_types,id',
            'district_id' => 'required|integer|exists:districts,id',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'area_m2' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:0',
            'year_built' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'manager' => 'nullable|string|max:255',
            'state' => 'required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $venue = Venue::create($validated);

        return response()->json([
            'message' => 'Venue berhasil ditambahkan.',
            'data' => $venue->load(['venueType', 'district']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $venue = Venue::with([
            'venueType',
            'district',
            'inspections' => fn ($q) => $q->orderBy('inspected_on', 'desc'),
            'maintenanceRecords' => fn ($q) => $q->orderBy('done_on', 'desc'),
            'venueUsages' => fn ($q) => $q->orderBy('month', 'desc'),
            'venueRevenues' => fn ($q) => $q->orderBy('month', 'desc'),
        ])->findOrFail($id);

        return response()->json([
            'data' => $venue,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $venue = Venue::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'venue_type_id' => 'sometimes|required|integer|exists:venue_types,id',
            'district_id' => 'sometimes|required|integer|exists:districts,id',
            'address' => 'sometimes|required|string|max:500',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'area_m2' => 'sometimes|required|numeric|min:0',
            'capacity' => 'sometimes|required|integer|min:0',
            'year_built' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'manager' => 'nullable|string|max:255',
            'state' => 'sometimes|required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $venue->update($validated);

        return response()->json([
            'message' => 'Venue berhasil diperbarui.',
            'data' => $venue->load(['venueType', 'district']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $venue = Venue::findOrFail($id);
        $venue->delete();

        return response()->json([
            'message' => 'Venue berhasil dihapus.',
        ]);
    }
}
