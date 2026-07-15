<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Artist extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'social_media_handle',
        'contact_email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studio(): HasOne
    {
        return $this->hasOne(Studio::class);
    }

    public function home(): HasOne
    {
        return $this->hasOne(Home::class);
    }

    public function featurePreferences(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'artist_feature_preference');
    }
}