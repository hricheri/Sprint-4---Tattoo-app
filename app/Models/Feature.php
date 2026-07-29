<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    protected $fillable = [
    'name',
    'category',
    ];

    public function studios(): BelongsToMany
    {
        return $this->belongsToMany(Studio::class, 'studio_feature');
    }

    public function artistPreferences(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_feature_preference');
    }
}