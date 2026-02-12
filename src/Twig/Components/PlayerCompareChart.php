<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Model\PlayerStatsEnum;
use App\Repository\PlayerRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PlayerCompareChart extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Player $player = null;

    #[LiveProp]
    public ?Player $player2 = null;

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
        $chart = $this->chartBuilder->createChart(Chart::TYPE_RADAR);

        $dataSets= [];

        if ($this->player instanceof Player) {
            $dataSets[] = $this->getDataSets($this->player, $this->getPlayerData($this->player));
        }

        if ($this->player2 instanceof Player) {
            $dataSets[] = $this->getDataSets(
                $this->player2,
                $this->getPlayerData($this->player2),
                'oklch(62% 0.194 149.214)',
                'oklch(62% 0.194 149.214/ 50%)'
            );
        }

        $chart->setData([ 'labels' => array_values($this->getDataKeys()), 'datasets' => $dataSets ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => ['display' => false],
                'datalabels' => [ 'display' => true, 'align' => 'center', 'anchor' => 'center' ],
            ],
            'layout' => [ 'padding' => 30 ],
            'scales' => [
                'r' => [
                    'grid' => [ 'display' => true, 'color' => 'oklch(100% 0 0)' ],
                    'angleLines' => [ 'display' => true, 'color' => 'oklch(100% 0 0)' ],
                    'pointLabels' => [
                        'display' => true,
                        'font' => ['size' => 11, 'weight' => 'bold'],
                        'color' => 'oklch(55% 0.046 257.417)',
                    ],
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ]
            ],
        ]);

        return $chart;
    }

    #[LiveListener('selectPlayer1')]
    public function setPlayer(#[LiveArg] ?Player $player): void
    {
        $this->player = $player;
    }

    #[LiveListener('selectPlayer2')]
    public function setPlayer2(#[LiveArg] ?Player $player): void
    {
        $this->player2 = $player;
    }

    public function getDataSets(
        ?Player $player,
        array $data,
        string $color = 'oklch(70% 0.191 22.216)',
        string $backGroundColor = 'oklch(70% 0.191 22.216 / 50%)'
    ): array {
        return [
            'label' => $player ? $player->getName() : $this->translator->trans('player', [], 'player'),
            'data' => array_values($data),
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

    /**
     * @throws Exception
     */
    public function getPlayerData(Player $player): array
    {
        $statsPlayer = $this->playerRepository->getPercentileStats($player->getId());

        $data = [];
        foreach ($this->getDataKeys() as $key => $translation) {
            $data[] = $statsPlayer[$key];
        }

        return $data;
    }

    public function getDataKeys(): array
    {
        return [
            PlayerStatsEnum::fg_pct->value => PlayerStatsEnum::fg_pct->trans($this->translator),
            PlayerStatsEnum::pts->value => PlayerStatsEnum::pts->trans($this->translator),
            PlayerStatsEnum::usage_rate->value => PlayerStatsEnum::usage_rate->trans($this->translator),
            PlayerStatsEnum::three_pct->value => PlayerStatsEnum::three_pct->trans($this->translator),
            PlayerStatsEnum::ft_pct->value => PlayerStatsEnum::ft_pct->trans($this->translator),
            PlayerStatsEnum::ast->value => PlayerStatsEnum::ast->trans($this->translator),
            PlayerStatsEnum::ast_to_ratio->value => PlayerStatsEnum::ast_to_ratio->trans($this->translator),
            PlayerStatsEnum::off_reb->value => PlayerStatsEnum::off_reb->trans($this->translator),
            PlayerStatsEnum::def_reb->value => PlayerStatsEnum::def_reb->trans($this->translator),
            PlayerStatsEnum::stl->value => PlayerStatsEnum::stl->trans($this->translator),
            PlayerStatsEnum::blk->value => PlayerStatsEnum::blk->trans($this->translator),
            PlayerStatsEnum::fouls_avoidance->value => PlayerStatsEnum::fouls_avoidance->trans($this->translator),
        ];
    }
}
