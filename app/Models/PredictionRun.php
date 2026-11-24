<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PredictionRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'run_at',
        'user_id',
        'config_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'run_at' => 'datetime',
            'config_snapshot' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(PredictionResult::class, 'run_id');
    }
}
