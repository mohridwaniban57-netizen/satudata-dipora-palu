<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'design_life_years',
        'base_interval_days',
    ];

    protected function casts(): array
    {
        return [
            'design_life_years' => 'integer',
            'base_interval_days' => 'integer',
        ];
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }
}
