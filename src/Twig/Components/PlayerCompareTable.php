<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerCompareTable
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Player $player = null;

    #[LiveProp]
    public ?Player $player2 = null;

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
}
