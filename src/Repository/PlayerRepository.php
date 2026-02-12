<?php

namespace App\Repository;

use App\Entity\Player;
use App\Model\PlayerStatsEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function getCentilesForPlayer(int $playerId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT 
            id,
            ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(points AS DECIMAL) / NULLIF(games, 0)) ASC) * 100) as ' . PlayerStatsEnum::points->value . ',
            ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(blocks AS DECIMAL) / NULLIF(games, 0)) ASC) * 100) as ' . PlayerStatsEnum::block->value . ',
            ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(steals AS DECIMAL) / NULLIF(games, 0)) ASC) * 100) as ' . PlayerStatsEnum::steal->value . ',
            ROUND(PERCENT_RANK() OVER (
                ORDER BY (CAST(fields_goals AS DECIMAL) / NULLIF(fields_goals_attempts, 0)) ASC
            ) * 100) as ' . PlayerStatsEnum::field_goal->value . ',
            ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(offensive_rebounds + defensive_rebounds AS DECIMAL) / NULLIF(games, 0)) ASC) * 100) as ' . PlayerStatsEnum::rebound->value . ',
            ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(assists AS DECIMAL) / NULLIF(games, 0)) ASC) * 100) as ' . PlayerStatsEnum::assist->value . '
        FROM player
    ';

        $finalSql = "SELECT * FROM ($sql) as stats WHERE id = :id";
        $result = $conn->executeQuery($finalSql, ['id' => $playerId])->fetchAssociative();

        return $result ?: [
            'pts_centile' => 0,
            'reb_centile' => 0,
            'ast_centile' => 0,
            'blk_centile' => 0,
            'stl_centile' => 0,
            'trv_centile' => 0,
        ];
    }

    /**
     * @throws Exception
     */
    public function getPercentileStats(int $playerId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // On définit les 12 piliers du graphique
        $sql = '
        SELECT * FROM (
            SELECT 
                id,
                -- 1. Scoring (Points / Match)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(points AS DECIMAL) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::pts->value . ',
                -- 2. Taux d\'Usage (Volume d\'actions terminées)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY ((CAST(fields_goals_attempts AS DECIMAL) + (0.44 * CAST(free_throws_attempts AS DECIMAL)) + CAST(turnovers AS DECIMAL)) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::usage_rate->value . ',
                -- 3. Adresse globale (FG%)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(fields_goals AS DECIMAL) / NULLIF(fields_goals_attempts, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::fg_pct->value . ',
                -- 4. Adresse 3 points (3P%)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(three_fields_goals AS DECIMAL) / NULLIF(three_fields_goals_attempts, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::three_pct->value . ',
                -- 5. Adresse Lancers (FT%)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(free_throws AS DECIMAL) / NULLIF(free_throws_attempts, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::ft_pct->value . ',
                -- 6. Création (Assists / Match)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(assists AS DECIMAL) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::ast->value . ',
                -- 7. Protection de balle (Ratio AST/TOV)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(assists AS DECIMAL) / NULLIF(turnovers, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::ast_to_ratio->value . ',
                -- 8. Rebonds Offensifs
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(offensive_rebounds AS DECIMAL) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::off_reb->value . ',
                -- 9. Rebonds Défensifs
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(defensive_rebounds AS DECIMAL) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::def_reb->value . ',
                -- 10. Interceptions (Steals)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(steals AS DECIMAL) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::stl->value . ',
                -- 11. Contres (Blocks)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(blocks AS DECIMAL) / NULLIF(games, 0)) ASC) * 100), 0) as ' . PlayerStatsEnum::blk->value . ',
                -- 12. Discipline (Moins de fautes)
                COALESCE(ROUND(PERCENT_RANK() OVER (ORDER BY (CAST(personal_fouls AS DECIMAL) / NULLIF(games, 0)) DESC) * 100), 0) as ' . PlayerStatsEnum::fouls_avoidance->value . '
            FROM player
        ) as sub WHERE id = :id
    ';

        return $conn->executeQuery($sql, ['id' => $playerId])->fetchAssociative() ?: [];
    }
}
