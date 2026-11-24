<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AthleteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Athlete::with(['sport', 'district', 'achievements']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->input('sport_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        $paginator = $query->orderBy('id', 'desc')->paginate(20);

        // Map items so that admin sees decrypted name as 'name'
        $paginator->getCollection()->transform(function ($athlete) {
            $data = $athlete->toArray();
            $data['name'] = $athlete->name_encrypted; // Decrypted via Laravel encrypted cast
            return $data;
        });

        return response()->json($paginator);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_year' => 'required|integer|min:1950|max:' . date('Y'),
            'gender' => 'required|in:m,f',
            'sport_id' => 'required|integer|exists:sports,id',
            'district_id' => 'required|integer|exists:districts,id',
            'level' => 'required|in:club,district,province,national',
            'consent_recorded' => 'boolean',
            'active' => 'boolean',
            'state' => 'required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        $currentYear = (int) date('Y');
        $isMinor = ($currentYear - (int) $validated['birth_year']) < 18;

        // Auto-generate code if needed
        $lastId = (int) (Athlete::max('id') ?? 0) + 1;
        $code = 'ATL-' . str_pad((string) $lastId, 4, '0', STR_PAD_LEFT);

        $athlete = Athlete::create([
            'code' => $code,
            'name_encrypted' => $validated['name'],
            'birth_year' => $validated['birth_year'],
            'gender' => $validated['gender'],
            'sport_id' => $validated['sport_id'],
            'district_id' => $validated['district_id'],
            'level' => $validated['level'],
            'is_minor' => $isMinor,
            'consent_recorded' => $validated['consent_recorded'] ?? false,
            'active' => $validated['active'] ?? true,
            'state' => $validated['state'],
            'is_dummy' => $validated['is_dummy'] ?? false,
            'data_source' => $validated['data_source'] ?? 'Input Manual Dispora',
        ]);

        $response = $athlete->load(['sport', 'district'])->toArray();
        $response['name'] = $athlete->name_encrypted;

        return response()->json([
            'message' => 'Data atlet berhasil disimpan.',
            'data' => $response,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $athlete = Athlete::with(['sport', 'district', 'achievements'])->findOrFail($id);

        $data = $athlete->toArray();
        $data['name'] = $athlete->name_encrypted;

        return response()->json([
            'data' => $data,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $athlete = Athlete::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'birth_year' => 'sometimes|required|integer|min:1950|max:' . date('Y'),
            'gender' => 'sometimes|required|in:m,f',
            'sport_id' => 'sometimes|required|integer|exists:sports,id',
            'district_id' => 'sometimes|required|integer|exists:districts,id',
            'level' => 'sometimes|required|in:club,district,province,national',
            'consent_recorded' => 'boolean',
            'active' => 'boolean',
            'state' => 'sometimes|required|in:draft,validated,published',
            'is_dummy' => 'boolean',
            'data_source' => 'nullable|string|max:255',
        ]);

        if (isset($validated['name'])) {
            $athlete->name_encrypted = $validated['name'];
            unset($validated['name']);
        }

        if (isset($validated['birth_year'])) {
            $currentYear = (int) date('Y');
            $athlete->is_minor = ($currentYear - (int) $validated['birth_year']) < 18;
        }

        $athlete->fill($validated);
        $athlete->save();

        $data = $athlete->load(['sport', 'district'])->toArray();
        $data['name'] = $athlete->name_encrypted;

        return response()->json([
            'message' => 'Data atlet berhasil diperbarui.',
            'data' => $data,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $athlete = Athlete::findOrFail($id);
        $athlete->delete();

        return response()->json([
            'message' => 'Data atlet berhasil dihapus.',
        ]);
    }
}
