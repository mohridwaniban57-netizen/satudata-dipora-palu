<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\VenueType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index(): JsonResponse
    {
        $districts = District::withCount(['venues', 'athletes', 'youthOrgs'])->orderBy('name', 'asc')->get();

        return response()->json([
            'data' => $districts,
        ]);
    }

    public function venueTypes(): JsonResponse
    {
        $venueTypes = VenueType::withCount('venues')->orderBy('name', 'asc')->get();

        return response()->json([
            'data' => $venueTypes,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $district = District::with(['venues', 'youthOrgs'])->findOrFail($id);

        return response()->json([
            'data' => $district,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $district = District::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'population' => 'sometimes|required|integer|min:0',
        ]);

        $district->update($validated);

        return response()->json([
            'message' => 'Data kecamatan berhasil diperbarui.',
            'data' => $district,
        ]);
    }
}
