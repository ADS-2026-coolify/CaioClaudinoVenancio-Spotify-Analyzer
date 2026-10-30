<?php

namespace App\Enums;

enum MoodCategory: string
{
    case Rain = 'rain';
    case Party = 'party';
    case Focus = 'focus';
    case Relax = 'relax';
    case Nostalgia = 'nostalgia';
    case Workout = 'workout';
    case RoadTrip = 'road_trip';
    case LateNight = 'late_night';
    case Romantic = 'romantic';

    /**
     * Retorna o label em pt-BR para a categoria de humor.
     */
    public function label(): string
    {
        return match ($this) {
            self::Rain => 'Dia de Chuva',
            self::Party => 'Festa',
            self::Focus => 'Foco',
            self::Relax => 'Relaxar',
            self::Nostalgia => 'Nostalgia',
            self::Workout => 'Treino',
            self::RoadTrip => 'Viagem',
            self::LateNight => 'Noite Tarde',
            self::Romantic => 'Romântico',
        };
    }

    /**
     * Retorna o emoji representativo da categoria.
     */
    public function emoji(): string
    {
        return match ($this) {
            self::Rain => '🌧️',
            self::Party => '🎉',
            self::Focus => '🎯',
            self::Relax => '😌',
            self::Nostalgia => '💭',
            self::Workout => '💪',
            self::RoadTrip => '🚗',
            self::LateNight => '🌙',
            self::Romantic => '❤️',
        };
    }

    /**
     * Retorna palavras-chave para busca na API do Spotify.
     *
     * @return array<int, string>
     */
    public function searchKeywords(): array
    {
        return match ($this) {
            self::Rain => ['rainy day', 'melancholy', 'acoustic rain', 'sad songs', 'lo-fi rain'],
            self::Party => ['party hits', 'dance pop', 'club bangers', 'festa', 'party anthem'],
            self::Focus => ['focus music', 'study beats', 'concentration', 'instrumental focus', 'deep focus'],
            self::Relax => ['chill vibes', 'relaxing', 'calm', 'peaceful', 'ambient relax'],
            self::Nostalgia => ['throwback', 'nostalgia', 'classic hits', 'old school', 'retro'],
            self::Workout => ['workout', 'gym motivation', 'high energy', 'power workout', 'running'],
            self::RoadTrip => ['road trip', 'driving songs', 'travel', 'highway', 'adventure'],
            self::LateNight => ['late night', 'midnight', 'after hours', 'night vibes', 'nocturnal'],
            self::Romantic => ['love songs', 'romantic', 'romance', 'love ballads', 'date night'],
        };
    }

    /**
     * Retorna gêneros musicais relacionados à categoria.
     *
     * @return array<int, string>
     */
    public function relatedGenres(): array
    {
        return match ($this) {
            self::Rain => ['indie', 'acoustic', 'lo-fi', 'ambient', 'folk'],
            self::Party => ['pop', 'dance', 'electronic', 'reggaeton', 'funk'],
            self::Focus => ['classical', 'ambient', 'lo-fi', 'instrumental', 'post-rock'],
            self::Relax => ['chill', 'bossa nova', 'jazz', 'ambient', 'soft pop'],
            self::Nostalgia => ['classic rock', 'pop', '80s', '90s', 'mpb'],
            self::Workout => ['hip-hop', 'electronic', 'rock', 'trap', 'drum and bass'],
            self::RoadTrip => ['rock', 'indie', 'pop rock', 'country', 'alternative'],
            self::LateNight => ['r&b', 'jazz', 'soul', 'lo-fi', 'chillwave'],
            self::Romantic => ['r&b', 'soul', 'pop', 'mpb', 'bossa nova'],
        };
    }

    /**
     * Retorna a representação completa com emoji e label.
     */
    public function display(): string
    {
        return $this->emoji() . ' ' . $this->label();
    }

    /**
     * Retorna todos os valores como array associativo [value => display].
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->display(), self::cases()),
        );
    }
}
