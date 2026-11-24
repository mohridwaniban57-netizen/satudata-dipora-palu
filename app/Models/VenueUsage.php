<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueUsage extends Model
{
    use HasFactory;

    protected $table = 'venue_usage';

    protected $fillable = [
        'venue_id',
        'month',
        'visits',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'visits' => 'integer',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
