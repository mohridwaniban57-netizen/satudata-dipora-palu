<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueRevenue extends Model
{
    use HasFactory;

    protected $table = 'venue_revenue';

    protected $fillable = [
        'venue_id',
        'month',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
