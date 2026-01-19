<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Form\ComparisonType;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PlayerComparison extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?array $initialFormData = null;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly PlayerRepository $playerRepository
    ) {}

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ComparisonType::class, $this->initialFormData);
    }

    #[LiveAction]
    public function updateView(): void
    {
        // On ne fait rien ici, le simple fait d'appeler cette action
        // va synchroniser le formulaire et re-calculer getChart()
    }

    public function getChart(): Chart
    {
        $form = $this->getForm();
        $p1 = $form->get('player1')->getData();
        $p2 = $form->get('player2')->getData();

        $chart = $this->chartBuilder->createChart(Chart::TYPE_RADAR);

        // Configuration des données (même logique que précédemment)
        $chart->setData([
            'labels' => [
                'Points par match',
                'Assists',
                'Rebonds',
                'Blocks',
                'pourcentage au shoot',
                'Steals',
            ],
            'datasets' => [
                $this->getDataSets($p1),
                $this->getDataSets($p2, '#22c55e', 'rgba(34, 197, 94, 0.2)'),
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => ['display' => false],
                'datalabels' => [
                    'display' => true,
                    'align' => 'center',
                    'anchor' => 'center',
                ],
            ],
            'layout' => [
                'padding' => 30 // Ajoute de l'espace tout autour du graphique
            ],
            'scales' => [
                'r' => [
                    'grid' => ['color' => 'rgba(255, 255, 255, 0.1)'],
                    'angleLines' => ['color' => 'rgba(255, 255, 255, 0.1)'],
                    'pointLabels' => [
                        'padding' => 20,
                        'font' => [
                            'size' => 13
                        ],
                        'color' => '#94a3b8'
                    ],
                    'ticks' => ['display' => false],
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,

                ]
            ]
        ]);

        return $chart;
    }

    public function getStatsByPlayer(?Player $player = null): array
    {
        if (null === $player) {
            return [50,50,50,50,50,50,50];
        }

        $stats = $this->playerRepository->getCentilesForPlayer($player->getId());

        return [
            $stats['pts_centile'],
            $stats['ast_centile'],
            $stats['reb_centile'],
            $stats['blk_centile'],
            $stats['fgs_centile'],
            $stats['stl_centile'],
        ];
    }

    public function getDataSets(
        ?Player $player,
        string $color = '#3b82f6',
        string $backGroundColor = 'rgba(59, 130, 246, 0.2)'
    ): array {
        return [
            'label' => $player ? $player->getName() : 'Joueur 1',
            'data' => $this->getStatsByPlayer($player),
            'borderColor' => $color,
            'backgroundColor' => $backGroundColor,
            'pointRadius' => 12,
            'pointHoverRadius' => 15,
            'pointBackgroundColor' => $color,
            'pointBorderColor' => '#fff',
            'pointBorderWidth' => 2,
            'datalabels' => [
                'color' => '#fff',
                'font' => ['weight' => 'bold', 'size' => 10],
            ]
        ];
    }
}
