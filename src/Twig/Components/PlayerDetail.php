<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerDetail
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Player $player = null;

    #[LiveListener('selectPlayer')]
    public function setPlayer(#[LiveArg] ?Player $player): void
    {
        $this->player = $player;
    }
}
