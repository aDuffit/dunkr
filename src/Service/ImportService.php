<?php

namespace App\Service;

use App\Entity\League;
use App\Entity\Player;
use App\Entity\Team;
use App\Model\PlayerPositionEnum;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportService
{
    const array dataRowsName = [
        self::defaultGameColumnName => 'setGames',
        'mp' => 'setMinutesPlayed',
        'fg' => 'setFieldsGoals',
        'fga' => 'setFieldsGoalsAttempts',
        'fg3' => 'setThreeFieldsGoals',
        'fg3a' => 'setThreeFieldsGoalsAttempts',
        'ft' => 'setFreeThrows',
        'fta' => 'setFreeThrowsAttempts',
        'orb' => 'setOffensiveRebounds',
        'drb' => 'setDefensiveRebounds',
        'ast' => 'setAssists',
        'stl' => 'setSteals',
        'blk' => 'setBlocks',
        'tov' => 'setTurnovers',
        'pf' => 'setPersonalFouls',
        'pts' => 'setPoints',
    ];

    const string defaultNotFoundValue = 'xPathNotFound';
    const string defaultPlayerColumnName = 'player';
    const string defaultTeamColumnName = 'team_name';
    const string defaultGameColumnName = 'g';
    const string playerStatsUrl = '2026_totals';

    public static function  importPlayerInformation(
        Player $player,
        HttpClientInterface $client,
        EntityManagerInterface $em,
    ): void {
        $crawler = self::getCrawlerByUrl(
            sprintf('https://www.basketball-reference.com%s', $player->getUrlEntryPoint()),
            $client
        );

        $paragraphFounds = $crawler->filter('div#info div#meta div.nothumb p');

        // On renseigne la date de naissance
        $birthdateCrawler = $crawler->filter('div#info div#meta div.nothumb p span#necro-birth');

        if ($birthdateCrawler->count() > 0) {
            $birthdateStr = $birthdateCrawler->attr('data-birth');

            if (!empty($birthdateStr)) {
                $birthDate = \DateTime::createFromFormat('Y-m-d', $birthdateStr);
                $player->setBirthDate($birthDate);
            }
        }

        foreach ($paragraphFounds as $paragraphFound) {
            $content = $paragraphFound->textContent;

            // recherche des positions
            if (preg_match('/Position:\s*([a-zA-Z]+)/i', $content, $matches)) {
                $posMatch = trim($matches[1]);

                $positions = PlayerPositionEnum::fromScrapedValue($posMatch);
                $player->setPosition($positions);
                continue;
            }

            // Cherche n'importe quel nombre suivi de 'cm'
            preg_match('/(\d+)(?=cm)/', $content, $heightMatch);
            $height = $heightMatch[1] ?? null;

            if (null !== $height && ctype_digit($height)) {
                $player->setHeight((int) $height);
            }

            // Cherche n'importe quel nombre suivi de 'kg'
            preg_match('/(\d+)(?=kg)/', $content, $weightMatch);
            $weight = $weightMatch[1] ?? null;

            if (null !== $weight && ctype_digit($weight)) {
                $player->setWeight((int) $weight);
            }
        }

        $em->persist($player);
    }

    public static function importPlayerStatsByLeague(
        League $league,
        HttpClientInterface $client,
        EntityManagerInterface $em,
        PlayerRepository $playerRepository,
        TeamRepository $teamRepository,
    ): void {
        $crawler = self::getCrawlerByUrl(self::getPlayerStatUrlByLeague($league), $client);

        $rows = $crawler->filter('table[id="totals-stats-2026"] tbody tr:not(.thead)');

        $teams = $teamRepository->findAll();

        $rows->each(function (Crawler $row) use ($playerRepository, $teamRepository, $league, $em, $teams) {
            $player = self::getPlayerByRow($row, $playerRepository);
            $teamName = self::getDataByRowName($row, self::defaultTeamColumnName);

            $team = null;
            foreach ($teams as $currentTeam) {
                if ($teamName === $currentTeam->getName()) {
                    $team = $currentTeam;
                }
            }

            if (!$player->getTeam() instanceof Team) {
                $player->setTeam($team);
            }

            if ($player->getTeam() instanceof Team && $team instanceof Team && $team !== $player->getTeam()) {
                $player->setTeam($team);
            }

            $em->persist($player);
        });

        $em->flush();
    }

    public static function importTeamByLeague(
        League $league,
        HttpClientInterface $client,
        EntityManagerInterface $em,
        TeamRepository $teamRepository,
    ): void {
        $crawler = self::getCrawlerByLeagueName($league, $client);

        $leagueCode = $league->getCode();
        $rows = $crawler->filter("table[id='{$leagueCode}_standings'] tbody tr:not(.thead)");

        $rows->each(function (Crawler $row) use ($teamRepository, $league, $em) {
            $teamName = self::getDataByRowName($row, 'team', 'th');

            if (self::defaultNotFoundValue === $teamName) {
                throw new LogicException(sprintf('Team "%s" not found.', $teamName));
            }

            $team = $teamRepository->findOneByName($teamName);

            if (!$team instanceof Team) {
                $team = new Team();
            }

            $team->setName($teamName);
            $team->addLeague($league);

            $em->persist($team);
        });

        $em->flush();
    }

    private static function getPlayerStatUrlByLeague(League $league): string
    {
        return sprintf('%s/%s.html', $league->getCrawlerUrl(), self::playerStatsUrl);
    }

    private static function getCrawlerByUrl(string $url, HttpClientInterface $client): Crawler
    {
        $response = $client->request('GET', $url);

        return new Crawler($response->getContent());
    }

    private static function getCrawlerByLeagueName(League $league, HttpClientInterface $client): Crawler
    {
        return self::getCrawlerByUrl($league->getCrawlerUrl(), $client);
    }

    private static function getPlayerByRow(
        Crawler $row,
        PlayerRepository $playerRepository,
    ): Player {
        $playerName = self::getDataByRowName($row, self::defaultPlayerColumnName, 'th');

        if (self::defaultNotFoundValue === $playerName) {
            throw new LogicException(sprintf('Player "%s" not found.', $playerName));
        }

        $player = $playerRepository->findOneByName($playerName);

        $games = self::getDataByRowName($row, self::defaultTeamColumnName);

        // Si le joueur existe et qu'il n'a pas rejoué de match depuis le dernier import pas besoin de mise à jour.
        if ($player instanceof Player && $games === $player->getGames()) {
            return $player;
        }

        if (!$player instanceof Player) {
            $player = new Player();
        }

        $player->setName($playerName);
        $player->setUrlEntryPoint($row->filter('th[data-stat="player"] a')->attr('href'));

        // On renseigne les stats crawlées
        foreach (self::dataRowsName as $rowName => $setFnName) {
            $player->$setFnName(self::getDataByRowName($row, $rowName));
        }

        return $player;
    }

    private static function getDataByRowName(Crawler $row, string $rowName, string $rowType = 'td'): int|string
    {
        $value = $row
            ->filterXPath('.//' . $rowType . '[@data-stat="' . $rowName . '"]')
            ->text(self::defaultNotFoundValue);

        if (ctype_digit($value)) {
            return intval($value);
        }

        return $row
            ->filterXPath('.//' . $rowType . '[@data-stat="' . $rowName . '"]')
            ->text(self::defaultNotFoundValue);
    }
}