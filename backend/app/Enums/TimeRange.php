<?php

namespace App\Enums;

enum TimeRange: string
{
    case ShortTerm = 'short_term';
    case MediumTerm = 'medium_term';
    case LongTerm = 'long_term';

    /**
     * Retorna o label em pt-BR para o intervalo de tempo.
     */
    public function label(): string
    {
        return match ($this) {
            self::ShortTerm => 'Últimas 4 semanas',
            self::MediumTerm => 'Últimos 6 meses',
            self::LongTerm => 'Todos os tempos',
        };
    }

    /**
     * Retorna todos os valores como array associativo [value => label].
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->label(), self::cases()),
        );
    }
}
