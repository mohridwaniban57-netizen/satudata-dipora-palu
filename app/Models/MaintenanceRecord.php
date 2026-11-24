<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'done_on',
        'type',
        'cost',
        'is_incident',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'done_on' => 'date',
            'cost' => 'decimal:2',
            'is_incident' => 'boolean',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
