<?php

namespace App\Model;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PlayerPositionEnum: string implements TranslatableInterface
{
    case point_guard = 'pg';
    case shooting_guard = 'sg';
    case small_forward = 'sf';
    case power_forward = 'pf';
    case center = 'c';

    public static function fromScrapedValue(string $value): array
    {
        // On nettoie et on met en minuscule
        $clean = strtolower(trim($value));

        return match (true) {
            str_contains($clean, 'point guard') => [self::point_guard],
            str_contains($clean, 'shooting guard') => [self::shooting_guard],
            str_contains($clean, 'small forward') => [self::small_forward],
            str_contains($clean, 'power forward') => [self::power_forward],
            str_contains($clean, 'center') => [self::center],
            str_contains($clean, 'guard') => [self::point_guard, self::shooting_guard],
            str_contains($clean, 'forward') => [self::small_forward, self::power_forward],
            default => [],
        };
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::point_guard => $translator->trans('player_position.point_guard', domain: 'model', locale: $locale),
            self::shooting_guard => $translator->trans('player_position.shooting_guard', domain: 'model', locale: $locale),
            self::small_forward => $translator->trans('player_position.small_forward', domain: 'model', locale: $locale),
            self::power_forward => $translator->trans('player_position.power_forward', domain: 'model', locale: $locale),
            self::center => $translator->trans('player_position.center', domain: 'model', locale: $locale),
        };
    }
}
