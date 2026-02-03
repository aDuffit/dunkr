<?php

namespace App\Model;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PlayerStatsEnum: string implements TranslatableInterface
{
    case points = 'pts_centile';
    case rebound = 'reb_centile';
    case assist = 'ast_centile';
    case block = 'blk_centile';
    case steal = 'stl_centile';
    case field_goal = 'fgs_centile';
    case pts = 'pts';
    case usage_rate = 'usage_rate';
    case fg_pct = 'fg_pct';
    case three_pct = 'three_pct';
    case ft_pct = 'ft_pct';
    case ast = 'ast';
    case ast_to_ratio = 'ast_to_ratio';
    case off_reb = 'off_reb';
    case def_reb = 'def_reb';
    case stl = 'stl';
    case blk = 'blk';
    case fouls_avoidance = 'fouls_avoidance';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::points, self::pts => $translator->trans('player_stats.point', domain: 'model', locale: $locale),
            self::rebound => $translator->trans('player_stats.rebound', domain: 'model', locale: $locale),
            self::assist, self::ast => $translator->trans('player_stats.assist', domain: 'model', locale: $locale),
            self::block, self::blk => $translator->trans('player_stats.block', domain: 'model', locale: $locale),
            self::steal, self::stl => $translator->trans('player_stats.steal', domain: 'model', locale: $locale),
            self::field_goal, self::fg_pct => $translator->trans('player_stats.field_goal', domain: 'model', locale: $locale),
            self::fouls_avoidance => $translator->trans('player_stats.fouls_avoidance', domain: 'model', locale: $locale),
            self::def_reb => $translator->trans('player_stats.def_reb', domain: 'model', locale: $locale),
            self::off_reb => $translator->trans('player_stats.off_reb', domain: 'model', locale: $locale),
            self::ast_to_ratio => $translator->trans('player_stats.ast_to_ratio', domain: 'model', locale: $locale),
            self::ft_pct => $translator->trans('player_stats.ft_pct', domain: 'model', locale: $locale),
            self::three_pct => $translator->trans('player_stats.three_pct', domain: 'model', locale: $locale),
            self::usage_rate => $translator->trans('player_stats.usage_rate', domain: 'model', locale: $locale),
        };
    }
}
