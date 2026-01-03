<?php

namespace App\Twig\Components;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerComparison
{
    use DefaultActionTrait;

    #[LiveProp]
    public Player $player; // Le joueur de base (passé par la page show)

    #[LiveProp(writable: true)]
    public bool $isComparing = false; // État de l'interface

    #[LiveProp(writable: true)]
    public ?int $compareId = null; // L'ID du rival choisi dans le select

    public function __construct(private readonly PlayerRepository $playerRepository) {}

    // Récupère l'objet complet du rival si un ID est sélectionné
    public function getComparePlayer(): ?Player
    {
        return $this->compareId ? $this->playerRepository->find($this->compareId) : null;
    }

    // Récupère tous les joueurs pour le select (sauf le joueur actuel)
    public function getAllPlayers(): array
    {
        return $this->playerRepository->createQueryBuilder('p')
            ->where('p.id != :id')
            ->setParameter('id', $this->player->getId())
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    #[LiveAction]
    public function toggleComparison(): void
    {
        $this->isComparing = !$this->isComparing;
        if (!$this->isComparing) {
            $this->compareId = null;
        }
    }
}
