<?php

namespace App\Models;

use App\Enums\TimeRange;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'type',
    'time_range',
    'data',
    'fetched_at',
])]
class AnalyticsSnapshot extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'fetched_at' => 'datetime',
            'time_range' => TimeRange::class,
        ];
    }

    /**
     * Usuário dono deste snapshot.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por tipo de snapshot.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para filtrar por intervalo de tempo.
     */
    public function scopeOfTimeRange(Builder $query, TimeRange|string $timeRange): Builder
    {
        $value = $timeRange instanceof TimeRange ? $timeRange->value : $timeRange;

        return $query->where('time_range', $value);
    }

    /**
     * Scope para filtrar por tipo e intervalo de tempo simultaneamente.
     */
    public function scopeForTypeAndRange(Builder $query, string $type, TimeRange|string $timeRange): Builder
    {
        return $query->ofType($type)->ofTimeRange($timeRange);
    }
}
