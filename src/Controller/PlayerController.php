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
    public function show(Player $player, PlayerRepository $playerRepository, ChartBuilderInterface $chartBuilder): Response
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

        // 2. Préparation du graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_POLAR_AREA);

        // Logique pour les couleurs en fonction du centile
        $colors = [];
        foreach ($centiles as $value) {
            if ($value >= 90) $colors[] = '#10b981'; // Vert émeraude (Elite)
            elseif ($value >= 70) $colors[] = '#84cc16'; // Vert clair (Bon)
            elseif ($value >= 50) $colors[] = '#eab308'; // Jaune (Moyen)
            elseif ($value >= 25) $colors[] = '#f97316'; // Orange (Faible)
            else $colors[] = '#ef4444'; // Rouge (Très faible)
        }

        $stats = $playerRepository->getAdvancedStats($player->getId());

        $data = [
            'Points' => $stats['pts'], 'Usage' => $stats['usage_rate'],             // Famille Attaque
            'FG%' => $stats['fg_pct'], '3P%' => $stats['three_pct'], 'FT%' => $stats['ft_pct'], // Famille Adresse
            'Assists' => $stats['ast'], 'Ratio A/T' => $stats['ast_to_ratio'],      // Famille Création
            'Reb Off' => $stats['off_reb'], 'Reb Def' => $stats['def_reb'],         // Famille Rebonds
            'Steals' => $stats['stl'], 'Blocks' => $stats['blk'],                  // Famille Défense
            'Fautes' => $stats['fouls_avoidance']                                  // Famille Discipline
        ];

        $colors = [
            '#3b82f6', '#3b82f6',                // Bleus
            '#10b981', '#10b981', '#10b981',      // Verts
            '#8b5cf6', '#8b5cf6',                // Violets
            '#f59e0b', '#f59e0b',                // Oranges
            '#ef4444', '#ef4444',                // Rouges
            '#64748b'                            // Gris
        ];

        $chart->setData([
            'labels' => array_keys($data),
            'datasets' => [[
                'data' => array_values($data),
                'backgroundColor' => $colors,
                'borderWidth' => 2,
                'borderColor' => '#ffffff' // Pour bien séparer les pétales
            ]]
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                    'grid' => ['display' => true, 'color' => 'rgba(255, 255, 255, 0.05)'],
                    'angleLines' => ['display' => true, 'color' => 'rgba(255, 255, 255, 0.1)'],
                    'ticks' => ['display' => false], // Cache les chiffres 20, 40, 60...
                    'pointLabels' => [
                        'display' => true, // Affiche les noms autour
                        'centerPointLabels' => true,
                        'font' => ['size' => 11, 'weight' => 'bold'],
                        'color' => '#94a3b8'
                    ]
                ]
            ],
            'plugins' => [
                'legend' => ['display' => false],
                'datalabels' => [
                    'color' => '#000', // Chiffres en noir sur les segments colorés
                    'font' => ['weight' => 'bold', 'size' => 12],
                    'display' => true
                ]
            ]
        ]);

        return $this->render('player/show.html.twig', [
            'player' => $player,
            'centiles' => $centiles,
            'top_percentage' => $topPercentage,
            'best_category' => $categoryLabels[$bestCategory] ?? 'Global',
            'chart' => $chart,
        ]);
    }
}
