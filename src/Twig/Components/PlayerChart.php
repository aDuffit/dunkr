<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Model\PlayerStatsEnum;
use App\Repository\PlayerRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerChart
{
    use DefaultActionTrait;

    public Player $player;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly PlayerRepository $playerRepository,
        private readonly TranslatorInterface $translator,
    ) {}

    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_POLAR_AREA);
        $stats = $this->playerRepository->getAdvancedStats($this->player->getId());

        $data = [
            PlayerStatsEnum::pts->trans($this->translator) => $stats[PlayerStatsEnum::pts->value],
            PlayerStatsEnum::usage_rate->trans($this->translator) => $stats[PlayerStatsEnum::usage_rate->value],
            PlayerStatsEnum::fg_pct->trans($this->translator) => $stats[PlayerStatsEnum::fg_pct->value],
            PlayerStatsEnum::three_pct->trans($this->translator) => $stats[PlayerStatsEnum::three_pct->value],
            PlayerStatsEnum::ft_pct->trans($this->translator) => $stats[PlayerStatsEnum::ft_pct->value],
            PlayerStatsEnum::ast->trans($this->translator) => $stats[PlayerStatsEnum::ast->value],
            PlayerStatsEnum::ast_to_ratio->trans($this->translator) => $stats[PlayerStatsEnum::ast_to_ratio->value],
            PlayerStatsEnum::off_reb->trans($this->translator) => $stats[PlayerStatsEnum::off_reb->value],
            PlayerStatsEnum::def_reb->trans($this->translator) => $stats[PlayerStatsEnum::def_reb->value],
            PlayerStatsEnum::stl->trans($this->translator) => $stats[PlayerStatsEnum::stl->value],
            PlayerStatsEnum::blk->trans($this->translator) => $stats[PlayerStatsEnum::blk->value],
            PlayerStatsEnum::fouls_avoidance->trans($this->translator) => $stats[PlayerStatsEnum::fouls_avoidance->value],
        ];

        // Logique pour les couleurs en fonction du centile
        $colors = [];
        foreach ($data as $value) {
            if ($value >= 90) $colors[] = '#10b981'; // Vert émeraude (Elite)
            elseif ($value >= 70) $colors[] = '#84cc16'; // Vert clair (Bon)
            elseif ($value >= 50) $colors[] = '#eab308'; // Jaune (Moyen)
            elseif ($value >= 25) $colors[] = '#f97316'; // Orange (Faible)
            else $colors[] = '#ef4444'; // Rouge (Très faible)
        }

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
                    'color' => '#fff', // Chiffres en blanc sur les segments colorés
                    'font' => ['weight' => 'bold', 'size' => 12],
                    'display' => true
                ]
            ]
        ]);

        return $chart;
    }
}
