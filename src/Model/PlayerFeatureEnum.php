<?php

namespace App\Model;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PlayerFeatureEnum: string implements TranslatableInterface
{
    case scorer = 'sc';
    case rebond = 'rb';
    case defense = 'df';
    case assist = 'ast';
    case steals = 'st';
    case scorer_elite = 'sc_elite';
    case rebond_elite = 'rb_elite';
    case defense_elite = 'df_elite';
    case assist_elite = 'ast_elite';
    case less_turnover = 'lto';
    case fg = 'fg';
    case fg_three = 'fg_three';
    case fg_ft = 'fg_ft';
    case more_defense = 'more_defense';
    case more_fg = 'more_fg';
    case more_fg_three = 'more_fg_three';
    case more_fg_ft = 'more_fg_ft';
    case fg_elite = 'fg_elite';
    case fg_three_elite = 'fg_three_elite';
    case fg_ft_elite = 'fg_ft_elite';
    case pg_elite = 'pg_elite';
    case pg = 'pg';
    case center_elite = 'center_elite';
    case center = 'center';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::scorer => $translator->trans('player_feature.scorer', domain: 'model', locale: $locale),
            self::rebond => $translator->trans('player_feature.rebond', domain: 'model', locale: $locale),
            self::defense => $translator->trans('player_feature.defense', domain: 'model', locale: $locale),
            self::assist => $translator->trans('player_feature.assist', domain: 'model', locale: $locale),
            self::steals => $translator->trans('player_feature.steals', domain: 'model', locale: $locale),
            self::scorer_elite => $translator->trans('player_feature.scorer_elite', domain: 'model', locale: $locale),
            self::rebond_elite => $translator->trans('player_feature.rebond_elite', domain: 'model', locale: $locale),
            self::defense_elite => $translator->trans('player_feature.defense_elite', domain: 'model', locale: $locale),
            self::assist_elite => $translator->trans('player_feature.assist_elite', domain: 'model', locale: $locale),
            self::less_turnover => $translator->trans('player_feature.less_turnover', domain: 'model', locale: $locale),
            self::fg => $translator->trans('player_feature.fg', domain: 'model', locale: $locale),
            self::fg_three => $translator->trans('player_feature.fg_three', domain: 'model', locale: $locale),
            self::fg_ft => $translator->trans('player_feature.fg_ft', domain: 'model', locale: $locale),
            self::more_defense => $translator->trans('player_feature.more_defense', domain: 'model', locale: $locale),
            self::more_fg => $translator->trans('player_feature.more_fg', domain: 'model', locale: $locale),
            self::more_fg_three => $translator->trans('player_feature.more_fg_three', domain: 'model', locale: $locale),
            self::more_fg_ft => $translator->trans('player_feature.more_fg_ft', domain: 'model', locale: $locale),
            self::fg_elite => $translator->trans('player_feature.fg_elite', domain: 'model', locale: $locale),
            self::fg_three_elite => $translator->trans('player_feature.fg_three_elite', domain: 'model', locale: $locale),
            self::fg_ft_elite => $translator->trans('player_feature.fg_ft_elite', domain: 'model', locale: $locale),
            self::pg_elite => $translator->trans('player_feature.pg_elite', domain: 'model', locale: $locale),
            self::pg => $translator->trans('player_feature.pg', domain: 'model', locale: $locale),
            self::center_elite => $translator->trans('player_feature.center_elite', domain: 'model', locale: $locale),
            self::center => $translator->trans('player_feature.center', domain: 'model', locale: $locale),
        };
    }
}
