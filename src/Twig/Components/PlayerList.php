<?php

namespace App\Twig\Components;

use App\Repository\PlayerRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PlayerList
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onQueryUpdated', url: true)]
    public string $query = '';

    #[LiveProp(writable: true, onUpdated: 'onSortFieldUpdated', url: true)]
    public string $sortField = 'points'; // Tri par défaut

    #[LiveProp(writable: true, url: true)]
    public string $sortDirection = 'DESC';

    #[LiveProp(writable: true, url: true)]
    public int $page = 1; // Page actuelle

    private const int PER_PAGE = 20; // Nombre de joueurs par page
    private const string ALIAS = 'player'; // Nombre de joueurs par page

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
        $qr = $this->playerRepository->createQueryBuilder(self::ALIAS);
        $this->addWhereClause($qr);

        // On cherche les joueurs dont le nom contient la recherche
        return $qr
            ->orderBy(sprintf('%s.%s', self::ALIAS, $this->sortField), $this->sortDirection)
            ->setFirstResult(($this->page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE)
            ->getQuery()
            ->getResult();
    }

    // Utile pour afficher "Page X sur Y"
    public function getTotalPages(): int
    {
        $qr = $this->playerRepository->createQueryBuilder(self::ALIAS)
            ->select(sprintf('COUNT(%s.id)', self::ALIAS));
        $this->addWhereClause($qr);
        $count = $qr->getQuery()->getSingleScalarResult();

        return ceil($count / self::PER_PAGE);
    }

    private function addWhereClause(QueryBuilder $qr): void
    {
        $qr
            ->where(sprintf('Upper(%s.name) LIKE :q', self::ALIAS))
            ->setParameter('q', '%' . strtoupper($this->query) . '%');
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
