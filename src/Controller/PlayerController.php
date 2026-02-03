<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/player')]
final class PlayerController extends AbstractController
{
    #[Route(name: 'app_player_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('player/index.html.twig');
    }

    #[Route('/compare', name: 'app_player_compare', methods: ['GET'])]
    public function compare(): Response
    {
        return $this->render('player/comparison.html.twig');
    }

    #[Route('/{id}', name: 'app_player_show', methods: ['GET'])]
    public function show(Player $player, PlayerRepository $playerRepository): Response
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

        return $this->render('player/show.html.twig', [
            'player' => $player,
            'centiles' => $centiles,
            'top_percentage' => $topPercentage,
            'best_category' => $categoryLabels[$bestCategory] ?? 'Global',
        ]);
    }
}
