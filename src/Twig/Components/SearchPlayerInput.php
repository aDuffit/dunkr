<?php

namespace App\Twig\Components;

use App\Repository\PlayerRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SearchPlayerInput
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public bool $isSelected = false;

    #[LiveProp(writable: true)]
    public string $eventName = 'selectPlayer';

    #[LiveProp]
    public string $inputClass = '';

    public function __construct(private readonly PlayerRepository $playerRepository)
    {
    }

    #[LiveAction]
    public function selectPlayer(#[LiveArg] int $id, #[LiveArg] string $name): void
    {
        $this->query = $name;
        $this->isSelected = true;
        $this->emit($this->eventName, [ 'player' => $id ]);
    }

    public function getPlayers(): array
    {
        if (empty($this->query)) {
            return [];
        }

        if ($this->isSelected) {
            return [];
        }

        // example method that returns an array of Products
        return $this->playerRepository->createQueryBuilder('player')
            ->where('lower(player.name) LIKE :query')
            ->setParameter('query', '%' . strtolower($this->query) . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
