<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Budget;
use App\Models\MedalHistory;
use App\Models\Venue;
use App\Models\YouthOrg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TransitionController extends Controller
{
    protected array $entityMap = [
        'venues' => Venue::class,
        'athletes' => Athlete::class,
        'medals' => MedalHistory::class,
        'medal-histories' => MedalHistory::class,
        'budgets' => Budget::class,
        'youth-orgs' => YouthOrg::class,
    ];

    /**
     * Transition a record's lifecycle state (draft -> validated -> published).
     */
    public function transition(Request $request, string $entity, int $id): JsonResponse
    {
        $validated = $request->validate([
            'to' => 'required|string|in:draft,validated,published',
        ]);

        $entityKey = strtolower($entity);
        if (!isset($this->entityMap[$entityKey])) {
            return response()->json([
                'message' => "Entitas '{$entity}' tidak mendukung transisi status lifecycle.",
            ], 400);
        }

        $modelClass = $this->entityMap[$entityKey];
        $record = $modelClass::findOrFail($id);

        $fromState = $record->state;
        $toState = $validated['to'];

        $record->state = $toState;
        $record->save();

        // Clear public caches if published state changes
        Cache::forget('public_summary_kpi');

        return response()->json([
            'message' => "Status {$entity} #{$id} berhasil diubah dari '{$fromState}' ke '{$toState}'.",
            'data' => [
                'entity' => $entity,
                'id' => $id,
                'from_state' => $fromState,
                'to_state' => $toState,
            ],
        ]);
    }
}
