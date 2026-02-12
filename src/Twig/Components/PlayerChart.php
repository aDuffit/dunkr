<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Model\PlayerStatsEnum;
use App\Repository\PlayerRepository;
use Doctrine\DBAL\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PlayerChart
{
    public Player $player;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly PlayerRepository $playerRepository,
        private readonly TranslatorInterface $translator,
    ) {}

    /**
     * @throws Exception
     */
    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_POLAR_AREA);
        $stats = $this->playerRepository->getPercentileStats($this->player->getId());

        $data = [
            PlayerStatsEnum::fg_pct->trans($this->translator) => $stats[PlayerStatsEnum::fg_pct->value],
            PlayerStatsEnum::pts->trans($this->translator) => $stats[PlayerStatsEnum::pts->value],
            PlayerStatsEnum::usage_rate->trans($this->translator) => $stats[PlayerStatsEnum::usage_rate->value],
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
            if ($value >= 90) $colors[] = 'oklch(60% 0.118 184.704)'; // Vert émeraude (Elite)
            elseif ($value >= 70) $colors[] = 'oklch(62% 0.194 149.214)'; // Vert clair (Bon)
            elseif ($value >= 50) $colors[] = 'oklch(58% 0.158 241.966)'; // Jaune (Moyen)
            elseif ($value >= 25) $colors[] = 'oklch(85% 0.199 91.936)'; // Orange (Faible)
            else $colors[] = 'oklch(70% 0.191 22.216)'; // Rouge (Très faible)
        }

        $chart->setData([
            'labels' => array_keys($data),
            'datasets' => [[
                'data' => array_values($data),
                'backgroundColor' => $colors,
                'borderWidth' => 2,
                'borderColor' => 'oklch(100% 0 0)'
            ]]
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                    'grid' => [
                        'display' => true,
                        'color' => 'oklch(100% 0 0)',
                    ],
                    'angleLines' => [
                        'display' => true,
                        'color' => 'oklch(100% 0 0)',
                    ],
                    'pointLabels' => [
                        'display' => true,
                        'font' => ['size' => 11, 'weight' => 'bold'],
                        'color' => 'oklch(55% 0.046 257.417)',
                        'centerPointLabels' => true,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => false],
                'datalabels' => [
                    'color' => 'oklch(100% 0 0)',
                    'font' => ['weight' => 'bold', 'size' => 12],
                    'display' => true,
                ],
            ],
        ]);

        return $chart;
    }
}
