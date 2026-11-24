<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'year',
        'amount',
        'state',
        'is_dummy',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'amount' => 'decimal:2',
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
