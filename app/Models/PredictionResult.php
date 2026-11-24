<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'run_id',
        'kind',
        'subject_ref',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function predictionRun(): BelongsTo
    {
        return $this->belongsTo(PredictionRun::class, 'run_id');
    }
}
