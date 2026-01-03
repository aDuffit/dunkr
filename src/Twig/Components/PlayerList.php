<?php

namespace App\Twig\Components;

use App\Repository\PlayerRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerList
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onQueryUpdated')]
    public string $query = '';

    #[LiveProp(writable: true, onUpdated: 'onSortFieldUpdated')]
    public string $sortField = 'pointsAvg'; // Tri par défaut

    #[LiveProp(writable: true)]
    public string $sortDirection = 'DESC';

    #[LiveProp(writable: true)]
    public int $page = 1; // Page actuelle

    private const PER_PAGE = 20; // Nombre de joueurs par page

    public function __construct(private readonly PlayerRepository $playerRepository) {}

    #[LiveAction]
    public function changeSort(#[LiveArg] string $field): void
    {
        if ($this->sortField === $field) {
            // Si on clique sur la même colonne, on inverse la direction
            $this->sortDirection = $this->sortDirection === 'DESC' ? 'ASC' : 'DESC';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'DESC';
        }
    }

    public function getPlayers(): array
    {
        // On cherche les joueurs dont le nom contient la recherche
        return $this->playerRepository->createQueryBuilder('p')
            ->where('p.name LIKE :q')
            ->setParameter('q', '%'.$this->query.'%')
            ->orderBy('p.' . $this->sortField, $this->sortDirection)
            ->setFirstResult(($this->page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE)
            ->getQuery()
            ->getResult();
    }

    // Utile pour afficher "Page X sur Y"
    public function getTotalPages(): int
    {
        $count = $this->playerRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.name LIKE :q')
            ->setParameter('q', '%' . $this->query . '%')
            ->getQuery()
            ->getSingleScalarResult();

        return ceil($count / self::PER_PAGE);
    }

    public function onQueryUpdated(): void
    {
        $this->page = 1;
    }

    public function onSortFieldUpdated(): void
    {
        $this->page = 1;
    }
}
