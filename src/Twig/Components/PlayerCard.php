<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Service\PlayerRatingService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PlayerCard
{
    public Player $player;
    public string $variant = 'gold'; // gold, silver, bronze

    public function __construct(private readonly PlayerRatingService $ratingService) {}

    public function getOverallRating(): int
    {
        return $this->ratingService->getGlobalRating($this->player);
    }
}
