<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Form\ComparisonType;
use App\Model\PlayerStatsEnum;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
        private readonly PlayerRepository $playerRepository,
        private readonly TranslatorInterface $translator,
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

        $p2 = null;
        if ($form->has('player2')) {
            $p2 = $form->get('player2')->getData();
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_RADAR);

        $labels = [];
        foreach (self::getStatsKey() as $key) {
            $playerStatEnum = PlayerStatsEnum::from($key);

            $labels[] = $playerStatEnum instanceof PlayerStatsEnum ? $playerStatEnum->trans($this->translator) : '--';
        }

        $chart->setData([
            'labels' => $labels,
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
        $data = [];
        foreach (self::getStatsKey() as $key) {
            $data[$key] = 50;
        }

        if (null === $player) {
            return array_values($data);
        }

        $stats = $this->playerRepository->getAdvancedStats($player->getId());

        foreach ($data as $key => $value) {
            $data[$key] = $stats[$key];
        }

        return array_values($data);
    }

    public function getDataSets(
        ?Player $player,
        string $color = '#3b82f6',
        string $backGroundColor = 'rgba(59, 130, 246, 0.2)'
    ): array {
        return [
            'label' => $player ? $player->getName() : $this->translator->trans('player', [], 'player'),
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

    public static function getStatsKey(): array
    {
        return [
            PlayerStatsEnum::pts->value,
            PlayerStatsEnum::usage_rate->value,
            PlayerStatsEnum::fg_pct->value,
            PlayerStatsEnum::three_pct->value,
            PlayerStatsEnum::ft_pct->value,
            PlayerStatsEnum::ast->value,
            PlayerStatsEnum::ast_to_ratio->value,
            PlayerStatsEnum::off_reb->value,
            PlayerStatsEnum::def_reb->value,
            PlayerStatsEnum::stl->value,
            PlayerStatsEnum::blk->value,
            PlayerStatsEnum::fouls_avoidance->value,
        ];
    }
}
