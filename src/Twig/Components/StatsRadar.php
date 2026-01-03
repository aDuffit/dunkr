<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class StatsRadar
{
    public Player $player1;
    public ?Player $player2 = null;

    public function __construct(private ChartBuilderInterface $chartBuilder) {}

    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_RADAR);

        $datasets = [
            [
                'label' => $this->player1->getName(),
                'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                'borderColor' => 'rgb(79, 70, 229)',
                'pointBackgroundColor' => 'rgb(79, 70, 229)',
                'data' => [$this->player1->getPointsAvg(), $this->player1->getReboundsAvg(), $this->player1->getAssistsAvg(), 10, 15],
            ]
        ];

        if ($this->player2) {
            $datasets[] = [
                'label' => $this->player2->getName(),
                'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                'borderColor' => 'rgb(239, 68, 68)',
                'pointBackgroundColor' => 'rgb(239, 68, 68)',
                'data' => [$this->player2->getPointsAvg(), $this->player2->getReboundsAvg(), $this->player2->getAssistsAvg(), 8, 12],
            ];
        }

        $chart->setData([
            'labels' => ['PTS', 'REB', 'AST', 'DEF', 'POT'],
            'datasets' => $datasets,
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 30,
                    'ticks' => ['display' => false],
                    'grid' => ['color' => 'rgba(255, 255, 255, 0.1)']
                ]
            ],
            'plugins' => ['legend' => ['display' => false]]
        ]);

        return $chart;
    }
}
