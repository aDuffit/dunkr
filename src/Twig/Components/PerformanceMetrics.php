<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PerformanceMetrics
{
    public Player $player1;
    public ?Player $player2 = null;

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
