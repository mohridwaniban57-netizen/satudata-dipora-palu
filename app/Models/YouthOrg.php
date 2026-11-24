<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YouthOrg extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'district_id',
        'members',
        'activity_level',
        'state',
        'is_dummy',
        'data_source',
    ];

    protected function casts(): array
    {
        return [
            'members' => 'integer',
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
}
