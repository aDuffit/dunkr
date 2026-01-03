<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Service\PlayerRatingService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PerformanceMetrics
{
    public Player $player1;
    public ?Player $player2 = null;

    // src/Twig/Components/PerformanceMetrics.php
    public function __construct(private readonly PlayerRatingService $ratingService) {}

    public function getGrade(float $val, float $max): string
    {
        return $this->ratingService->getGrade($val, $max);
    }

    public function getMetrics(): array
    {
        return [
            ['label' => 'Points', 'key' => 'pointsAvg', 'max' => 35],
            ['label' => 'Rebonds', 'key' => 'reboundsAvg', 'max' => 15],
            ['label' => 'Assists', 'key' => 'assistsAvg', 'max' => 12],
            ['label' => 'Contres', 'key' => 'blocksAvg', 'max' => 5],
            ['label' => 'Interceptions', 'key' => 'stealsAvg', 'max' => 4],
        ];
    }
}
