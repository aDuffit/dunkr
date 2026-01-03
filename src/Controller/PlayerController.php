<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/player')]
final class PlayerController extends AbstractController
{
    #[Route(name: 'app_player_index', methods: ['GET'])]
    public function index(PlayerRepository $playerRepository): Response
    {
        return $this->render('player/index.html.twig', [
            'players' => $playerRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_player_show', methods: ['GET'])]
    public function show(Player $player, ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_RADAR);

        $chart->setData([
            'labels' => ['Scoring', 'Rebounds', 'Assists', 'Defense', 'Efficiency'],
            'datasets' => [
                [
                    'label' => $player->getName(),
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)', // Indigo 600
                    'borderColor' => 'rgb(79, 70, 229)',
                    'data' => [
                        $this->mapStatToGrade($player->getPointsAvg(), 30), // Max 30 pts
                        $this->mapStatToGrade($player->getReboundsAvg(), 15), // Max 15 reb
                        $this->mapStatToGrade($player->getAssistsAvg(), 12),  // Max 12 ast
                        75, // Note manuelle ou calculée pour la défense
                        80, // Note d'efficacité
                    ],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'r' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                    'ticks' => ['display' => false], // On cache les chiffres moches
                ],
            ],
        ]);

        return $this->render('player/show.html.twig', [
            'player' => $player,
            'chart' => $chart,
        ]);
    }

    private function mapStatToGrade($value, $max): int
    {
        return min(100, ($value / $max) * 100);
    }
}
