<?php

namespace App\Service;

use App\Entity\Player;
use App\Model\PlayerFeatureEnum;
use App\Model\PlayerPositionEnum;
use App\Repository\PlayerRepository;

class PlayerRatingService
{
    public static function getStrongPoints(Player $player): array
    {
        return [
            self::playerIsScorer($player),
            self::playerIsAssist($player),
            self::playerIsRebound($player),
            self::playerIsDefense($player),
            self::playerIsSteal($player),
            self::playerIsFg($player),
            self::playerIsFtFg($player),
            self::playerIsThreeFg($player),
            self::isCenter($player),
            self::isPg($player),
        ];
    }

    public static function getSoftPoints(Player $player): array
    {
        return [
            self::playerIsFg($player) ? false : PlayerFeatureEnum::more_fg,
            self::playerIsThreeFg($player) ? false : PlayerFeatureEnum::more_fg_three,
            self::playerIsFtFg($player) ? false : PlayerFeatureEnum::more_fg_ft,
            self::playerIsTurnover($player),
        ];
    }

    public static function playerIsScorer(Player $player): false|PlayerFeatureEnum
    {
        if ($player->getPointsByGames() >= 15) {
            return PlayerFeatureEnum::scorer_elite;
        }

        if ($player->getPointsByGames() >= 8) {
            return PlayerFeatureEnum::scorer;
        }

        return false;
    }

    public static function playerIsAssist(Player $player): false|PlayerFeatureEnum
    {
        if ($player->getAssistsByGames() >= 8) {
            return PlayerFeatureEnum::assist_elite;
        }

        if ($player->getAssistsByGames() >= 4) {
            return PlayerFeatureEnum::assist;
        }

        return false;
    }

    public static function playerIsRebound(Player $player): false|PlayerFeatureEnum
    {
        if ($player->getReboundsByGames() >= 8) {
            return PlayerFeatureEnum::rebond_elite;
        }

        if ($player->getReboundsByGames()  >= 4) {
            return PlayerFeatureEnum::rebond;
        }

        return false;
    }

    public static function playerIsDefense(Player $player): false|PlayerFeatureEnum
    {
        $defenseTot = $player->getStealsByGames() + $player->getBlocksByGames();

        if ($defenseTot >= 5) {
            return PlayerFeatureEnum::defense_elite;
        }

        if ($defenseTot  >= 3) {
            return PlayerFeatureEnum::defense;
        }

        return false;
    }

    public static function playerIsSteal(Player $player): false|PlayerFeatureEnum
    {
        return $player->getStealsByGames() >= 5 ? PlayerFeatureEnum::steals : false;
    }

    public static function playerIsTurnover(Player $player): false|PlayerFeatureEnum
    {
        return $player->getTurnovers() >= 3 ? PlayerFeatureEnum::less_turnover : false;
    }

    public static function playerIsFg(Player $player): false|PlayerFeatureEnum
    {
        if ($player->getFieldGoalsPercent() >= 50) {
            return PlayerFeatureEnum::fg_elite;
        }

        if ($player->getFieldGoalsPercent() >= 39) {
            return PlayerFeatureEnum::fg;
        }

        return false;
    }

    public static function playerIsThreeFg(Player $player): false|PlayerFeatureEnum
    {
        if ($player->getThreeFieldGoalsPercent() >= 45) {
            return PlayerFeatureEnum::fg_three_elite;
        }

        if ($player->getThreeFieldGoalsPercent() >= 35) {
            return PlayerFeatureEnum::fg_three;
        }

        return false;
    }

    public static function playerIsFtFg(Player $player): false|PlayerFeatureEnum
    {
        if ($player->getFreeThrowFieldGoalsPercent() >= 90) {
            return PlayerFeatureEnum::fg_ft_elite;
        }

        if ($player->getFreeThrowFieldGoalsPercent() >= 80) {
            return PlayerFeatureEnum::fg_ft;
        }

        return false;
    }

    public static function isPg(Player $player): false|PlayerFeatureEnum
    {
        if (!in_array(PlayerPositionEnum::point_guard, $player->getPosition())) {
            return false;
        }

        $pgTot = $player->getAssistsByGames() + $player->getPointsByGames() - $player->getTurnoversByGames();

        if ($pgTot >= 15) {
            return PlayerFeatureEnum::pg_elite;
        }

        if ($pgTot >= 10) {
            return PlayerFeatureEnum::pg;
        }

        return false;
    }

    public static function isCenter(Player $player): false|PlayerFeatureEnum
    {
        if (!in_array(PlayerPositionEnum::center, $player->getPosition())) {
            return false;
        }

        $centerTot = $player->getReboundsByGames() + $player->getPointsByGames() + $player->getBlocksByGames() - $player->getTurnoversByGames();

        if ($centerTot >= 20) {
            return PlayerFeatureEnum::center_elite;
        }

        if ($centerTot >= 15) {
            return PlayerFeatureEnum::center;
        }

        return false;
    }
}