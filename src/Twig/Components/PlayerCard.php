<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PlayerCard
{
    public Player $player;
    public string $variant = 'gold'; // gold, silver, bronze
}
