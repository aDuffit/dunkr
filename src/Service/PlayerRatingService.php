<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\PlayerRepository;

class PlayerRatingService
{
    public static function getStrongPoints(Player $player, PlayerRepository $playerRepository)
    {
        // 1. Récupération des centiles (via la méthode SQL qu'on a créée ensemble)
        $centiles = $playerRepository->getCentilesForPlayer($player->getId());

        // Calcul du meilleur centile pour le badge "Top %"
        // On inverse : si centile est 92, il est dans le Top (100 - 92) = 8%
        $maxCentile = max($centiles ?: [0]);
        $topPercentage = 100 - $maxCentile;

        // Déterminer quelle est sa meilleure catégorie pour le texte du badge
        $bestCategory = array_search($maxCentile, $centiles);
        $categoryLabels = [
            'pts_centile' => 'Points',
            'reb_centile' => 'Rebonds',
            'ast_centile' => 'Passes',
            'blk_centile' => 'Contres',
            'stl_centile' => 'Interceptions',
            'fgs_centile' => 'Efficacité'
        ];
    }
}