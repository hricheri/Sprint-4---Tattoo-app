<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Studio extends Model
{
    protected $fillable = [
        'artist_id',
        'name',
        'description',
        'city',
        'address',
        'cost_type',
        'cost_amount',
        'studio_type',
        'access_instructions',
        'photo',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'studio_feature');
    }
}