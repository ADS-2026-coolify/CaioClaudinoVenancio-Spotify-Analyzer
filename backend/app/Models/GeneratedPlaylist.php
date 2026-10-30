<?php

namespace App\Models;

use App\Enums\MoodCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'spotify_playlist_id',
    'name',
    'description',
    'mood',
    'track_count',
    'is_public',
    'spotify_url',
])]
class GeneratedPlaylist extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'mood' => MoodCategory::class,
            'track_count' => 'integer',
        ];
    }

    /**
     * Usuário que gerou esta playlist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
