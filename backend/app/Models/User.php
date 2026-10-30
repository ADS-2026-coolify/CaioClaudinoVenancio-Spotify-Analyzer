<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'spotify_id',
    'display_name',
    'name',
    'email',
    'avatar_url',
    'country',
    'product',
    'last_login_at',
])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Token de acesso do Spotify associado ao usuário.
     */
    public function spotifyToken(): HasOne
    {
        return $this->hasOne(SpotifyToken::class);
    }

    /**
     * Snapshots de analytics do usuário.
     */
    public function analyticsSnapshots(): HasMany
    {
        return $this->hasMany(AnalyticsSnapshot::class);
    }

    /**
     * Playlists geradas pelo usuário.
     */
    public function generatedPlaylists(): HasMany
    {
        return $this->hasMany(GeneratedPlaylist::class);
    }
}
