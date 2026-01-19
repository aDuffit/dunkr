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

    #[ORM\Column(options: ['default' => 0])]
    private int $points = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $assists = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $games = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $minutesPlayed = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $fieldsGoals = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $fieldsGoalsAttempts = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $threeFieldsGoals = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $threeFieldsGoalsAttempts = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $freeThrows = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $freeThrowsAttempts = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $offensiveRebounds = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $defensiveRebounds = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $steals = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $blocks = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $turnovers = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $personalFouls = 0;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'players')]
    private ?Team $team = null;

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

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getAssists(): ?int
    {
        return $this->assists;
    }

    public function setAssists(int $assists): static
    {
        $this->assists = $assists;

        return $this;
    }

    public function getGames(): ?int
    {
        return $this->games;
    }

    public function setGames(int $games): static
    {
        $this->games = $games;

        return $this;
    }

    public function getMinutesPlayed(): ?int
    {
        return $this->minutesPlayed;
    }

    public function setMinutesPlayed(int $minutesPlayed): static
    {
        $this->minutesPlayed = $minutesPlayed;

        return $this;
    }

    public function getFieldsGoals(): ?int
    {
        return $this->fieldsGoals;
    }

    public function setFieldsGoals(int $fieldsGoals): static
    {
        $this->fieldsGoals = $fieldsGoals;

        return $this;
    }

    public function getFieldsGoalsAttempts(): ?int
    {
        return $this->fieldsGoalsAttempts;
    }

    public function setFieldsGoalsAttempts(int $fieldsGoalsAttempts): static
    {
        $this->fieldsGoalsAttempts = $fieldsGoalsAttempts;

        return $this;
    }

    public function getThreeFieldsGoals(): ?int
    {
        return $this->threeFieldsGoals;
    }

    public function setThreeFieldsGoals(int $threeFieldsGoals): static
    {
        $this->threeFieldsGoals = $threeFieldsGoals;

        return $this;
    }

    public function getThreeFieldsGoalsAttempts(): ?int
    {
        return $this->threeFieldsGoalsAttempts;
    }

    public function setThreeFieldsGoalsAttempts(int $threeFieldsGoalsAttempts): static
    {
        $this->threeFieldsGoalsAttempts = $threeFieldsGoalsAttempts;

        return $this;
    }

    public function getFreeThrows(): ?int
    {
        return $this->freeThrows;
    }

    public function setFreeThrows(int $freeThrows): static
    {
        $this->freeThrows = $freeThrows;

        return $this;
    }

    public function getFreeThrowsAttempts(): ?int
    {
        return $this->freeThrowsAttempts;
    }

    public function setFreeThrowsAttempts(int $freeThrowsAttempts): static
    {
        $this->freeThrowsAttempts = $freeThrowsAttempts;

        return $this;
    }

    public function getOffensiveRebounds(): ?int
    {
        return $this->offensiveRebounds;
    }

    public function setOffensiveRebounds(int $offensiveRebounds): static
    {
        $this->offensiveRebounds = $offensiveRebounds;

        return $this;
    }

    public function getDefensiveRebounds(): ?int
    {
        return $this->defensiveRebounds;
    }

    public function setDefensiveRebounds(int $defensiveRebounds): static
    {
        $this->defensiveRebounds = $defensiveRebounds;

        return $this;
    }

    public function getSteals(): ?int
    {
        return $this->steals;
    }

    public function setSteals(int $steals): static
    {
        $this->steals = $steals;

        return $this;
    }

    public function getBlocks(): ?int
    {
        return $this->blocks;
    }

    public function setBlocks(int $blocks): static
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function getTurnovers(): ?int
    {
        return $this->turnovers;
    }

    public function setTurnovers(int $turnovers): static
    {
        $this->turnovers = $turnovers;

        return $this;
    }

    public function getPersonalFouls(): ?int
    {
        return $this->personalFouls;
    }

    public function setPersonalFouls(int $personalFouls): static
    {
        $this->personalFouls = $personalFouls;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getRebounds(): ?int
    {
        return $this->getDefensiveRebounds() + $this->getOffensiveRebounds();
    }

    public function getMissedFieldGoals(): ?int
    {
        return $this->getFieldsGoalsAttempts() - $this->getFieldsGoals();
    }

    public function getMissedFreeThrows(): ?int
    {
        return $this->getFreeThrowsAttempts() - $this->getFreeThrows();
    }

    public function getEval(): ?int
    {
        return ($this->getPoints() + $this->getRebounds() + $this->getAssists()+ $this->getSteals() + $this->getBlocks())
            - ($this->getTurnovers() + $this->getMissedFieldGoals() + $this->getMissedFreeThrows());
    }

    public function getTrueShootingPercent(): float
    {
        return $this->getPoints() / ($this->getFieldsGoalsAttempts() + 0.44 * $this->getFreeThrowsAttempts());
    }

    public function getPointsByGames(): float|int
    {
        return $this->getPoints() / $this->getGames();
    }

    public function getReboundsByGames(): float|int
    {
        return $this->getRebounds() / $this->getGames();
    }

    public function getAssistsByGames(): float|int
    {
        return $this->getAssists() / $this->getGames();
    }

    public function getStealsByGames(): float|int
    {
        return $this->getSteals() / $this->getGames();
    }

    public function getTurnoversByGames(): float|int
    {
        return $this->getTurnovers() / $this->getGames();
    }

    public function getBlocksByGames(): float|int
    {
        return $this->getBlocks() / $this->getGames();
    }

    public function getFieldGoalsPercent(): float
    {
        return round($this->getFieldsGoals() / $this->getFieldsGoalsAttempts() * 100, 2);
    }
}
