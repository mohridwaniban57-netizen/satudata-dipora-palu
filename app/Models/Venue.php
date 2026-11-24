<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'venue_type_id',
        'district_id',
        'address',
        'latitude',
        'longitude',
        'area_m2',
        'capacity',
        'year_built',
        'manager',
        'state',
        'is_dummy',
        'data_source',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'area_m2' => 'float',
            'capacity' => 'integer',
            'year_built' => 'integer',
            'is_dummy' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('state', 'published');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function venueType(): BelongsTo
    {
        return $this->belongsTo(VenueType::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function venueUsages(): HasMany
    {
        return $this->hasMany(VenueUsage::class);
    }

    public function venueRevenues(): HasMany
    {
        return $this->hasMany(VenueRevenue::class);
    }
}
