<?php

namespace App\Service;

use App\Entity\Player;

class PlayerRatingService
{
    /**
     * Calcule une note globale (Général) sur 100
     */
    public function getGlobalRating(Player $player): int
    {
        $score = ($player->getPointsAvg() * 2) + ($player->getReboundsAvg() * 1.5) + ($player->getAssistsAvg() * 1.5);
        // On normalise : 40 pts/reb/ast cumulés = environ 90 de général
        return (int) min(99, max(40, $score * 1.8));
    }

    /**
     * Retourne un grade (A+, B, etc.) selon une valeur et un max
     */
    public function getGrade(float $value, float $max): string
    {
        $ratio = $value / $max;
        return match (true) {
            $ratio >= 0.9 => 'A+',
            $ratio >= 0.8 => 'A',
            $ratio >= 0.7 => 'B+',
            $ratio >= 0.6 => 'B',
            $ratio >= 0.5 => 'C',
            default => 'D',
        };
    }

    /**
     * Calcule le potentiel basé sur l'âge et les stats actuelles
     */
    public function getPotential(Player $player): int
    {
        $age = $player->getAge() ?? 20;
        $base = $this->getGlobalRating($player);

        // Plus le joueur est jeune, plus la marge de progression est grande
        $bonus = max(0, (25 - $age) * 2);
        return (int) min(99, $base + $bonus);
    }
}