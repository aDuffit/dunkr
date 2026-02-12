<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Service\PlayerRatingService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerFeature
{
    use DefaultActionTrait;

    #[LiveProp(updateFromParent: true)]
    public Player $player;

    public function getStrongPoints(): array
    {
        return PlayerRatingService::getStrongPoints($this->player);
    }

    public function getSoftPoints(): array
    {
        return PlayerRatingService::getSoftPoints($this->player);
    }
}
