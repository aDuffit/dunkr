<?php

namespace App\Model;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PlayerCentileEnum: string implements TranslatableInterface
{
    case points = 'pts_centile';
    case rebound = 'reb_centile';
    case assist = 'ast_centile';
    case block = 'blk_centile';
    case steal = 'stl_centile';
    case field_goal = 'fgs_centile';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::points => $translator->trans('player_centile.point', domain: 'model', locale: $locale),
            self::rebound => $translator->trans('player_centile.rebound', domain: 'model', locale: $locale),
            self::assist => $translator->trans('player_centile.assist', domain: 'model', locale: $locale),
            self::block => $translator->trans('player_centile.block', domain: 'model', locale: $locale),
            self::steal => $translator->trans('player_centile.steal', domain: 'model', locale: $locale),
            self::field_goal => $translator->trans('player_centile.field_goal', domain: 'model', locale: $locale),
        };
    }
}
