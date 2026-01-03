<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $team = null;

    #[ORM\Column(options: ['default' => 0])]
    private float $pointsAvg = 0;

    #[ORM\Column(options: ['default' => 0])]
    private float $reboundsAvg = 0;

    #[ORM\Column(options: ['default' => 0])]
    private float $assistsAvg = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getPointsAvg(): ?float
    {
        return $this->pointsAvg;
    }

    public function setPointsAvg(?float $pointsAvg): static
    {
        $this->pointsAvg = $pointsAvg;

        return $this;
    }

    public function getReboundsAvg(): ?float
    {
        return $this->reboundsAvg;
    }

    public function setReboundsAvg(?float $reboundsAvg): static
    {
        $this->reboundsAvg = $reboundsAvg;

        return $this;
    }

    public function getAssistsAvg(): ?float
    {
        return $this->assistsAvg;
    }

    public function setAssistsAvg(float $assistsAvg): static
    {
        $this->assistsAvg = $assistsAvg;

        return $this;
    }
}
