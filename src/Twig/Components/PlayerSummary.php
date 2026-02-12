<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PlayerSummary
{
    public Player $player;
}
