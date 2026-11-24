<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->venueType?->name,
            'type_id' => $this->venue_type_id,
            'district' => $this->district?->name,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'capacity' => (int) $this->capacity,
            'area_m2' => (float) $this->area_m2,
            'year_built' => (int) $this->year_built,
            'manager' => $this->manager,
            'is_dummy' => (bool) $this->is_dummy,
            'data_source' => $this->data_source,
            'condition' => [
                'condition_score' => $this->condition_metrics['condition_score'] ?? null,
                'status' => $this->condition_metrics['status'] ?? null,
                'risk_score' => $this->condition_metrics['risk_score'] ?? null,
                'age_years' => $this->condition_metrics['age_years'] ?? null,
                'latest_inspection_score' => $this->condition_metrics['latest_inspection_score'] ?? null,
                'last_maintenance_date' => $this->condition_metrics['last_maintenance_date'] ?? null,
                'next_maintenance_date' => $this->condition_metrics['next_maintenance_date'] ?? null,
                'priority_rank' => $this->condition_metrics['priority_rank'] ?? null,
            ],
            'inspections' => $this->inspections->sortByDesc('inspected_on')->values()->map(fn ($i) => [
                'id' => $i->id,
                'inspected_on' => $i->inspected_on,
                'score' => (int) $i->score,
                'notes' => $i->notes,
            ]),
            'maintenance_records' => $this->maintenanceRecords->sortByDesc('done_on')->values()->map(fn ($m) => [
                'id' => $m->id,
                'done_on' => $m->done_on,
                'type' => $m->type,
                'cost' => (float) $m->cost,
                'is_incident' => (bool) $m->is_incident,
                'description' => $m->description,
            ]),
            'usage_history' => $this->venueUsages->sortBy('month')->values()->map(fn ($u) => [
                'month' => $u->month->format('Y-m'),
                'visits' => (int) $u->visits,
            ]),
            'revenue_history' => $this->venueRevenues->sortBy('month')->values()->map(fn ($r) => [
                'month' => $r->month->format('Y-m'),
                'amount' => (float) $r->amount,
            ]),
        ];
    }
}
