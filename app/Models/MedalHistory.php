<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedalHistory extends Model
{
    use HasFactory;

    protected $table = 'medal_history';

    protected $fillable = [
        'sport_id',
        'event',
        'year',
        'gold',
        'silver',
        'bronze',
        'state',
        'is_dummy',
        'data_source',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'gold' => 'integer',
            'silver' => 'integer',
            'bronze' => 'integer',
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
}
