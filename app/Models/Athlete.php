<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Athlete extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_encrypted',
        'birth_year',
        'gender',
        'sport_id',
        'district_id',
        'level',
        'is_minor',
        'consent_recorded',
        'active',
        'state',
        'is_dummy',
        'data_source',
    ];

    protected function casts(): array
    {
        return [
            'name_encrypted' => 'encrypted',
            'birth_year' => 'integer',
            'is_minor' => 'boolean',
            'consent_recorded' => 'boolean',
            'active' => 'boolean',
            'is_dummy' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('state', 'published');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }
}
