<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PlayerId
{
    public Player $player;
}
