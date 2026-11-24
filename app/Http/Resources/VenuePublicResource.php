<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenuePublicResource extends JsonResource
{
    /**
     * Transform the resource into an array for public map/list.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->venueType?->name,
            'type_name' => $this->venueType?->name,
            'type_id' => $this->venue_type_id,
            'district' => $this->district?->name,
            'district_name' => $this->district?->name,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'capacity' => (int) $this->capacity,
            'area_m2' => (float) $this->area_m2,
            'year_built' => (int) $this->year_built,
            'is_dummy' => (bool) $this->is_dummy,
            'score' => $this->when(isset($this->condition_score), $this->condition_score),
            'condition_score' => $this->when(isset($this->condition_score), $this->condition_score),
            'status' => $this->when(isset($this->condition_status), strtolower($this->condition_status)),
            'usage_index' => $this->when(isset($this->usage_index), $this->usage_index),
        ];
    }
}
