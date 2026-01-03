<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/import')]
final class ImportController extends AbstractController
{
    #[Route('/player', name: 'app_import_player', methods: ['GET'])]
    public function import(EntityManagerInterface $em, HttpClientInterface $client): Response
    {
        $response = $client->request('GET', 'https://www.basketball-reference.com/international/euroleague/2026_totals.html');
        $crawler = new Crawler($response->getContent());


        // On cible spécifiquement le tableau des totaux
        $rows = $crawler->filterXPath('//table[contains(@id, "totals-stats")]/tbody/tr[not(contains(@class, "thead"))]');

        $rows->each(function (Crawler $node) use ($em) {
            $player = new Player();
            $player->setName($node->filterXPath('.//th[@data-stat="player"]')->text('Inconnu'));
//            $player->setPointsAvg((float) $node->filterXPath('//td[' . $columnMap['PPG'] . ']')->text());
//            $player->setReboundsAvg((float) $node->filterXPath('//td[' . $columnMap['RPG'] . ']')->text());
//            $player->setAssistsAvg((float) $node->filterXPath('//td[' . $columnMap['APG'] . ']')->text());


//            return [
//                'name'      => $node->filterXPath('.//td[@data-stat="player"]')->text('Inconnu'),
//                'team'      => $node->filterXPath('.//td[@data-stat="team_id"]')->text('N/A'),
//                'games'     => (int) $node->filterXPath('.//td[@data-stat="g"]')->text(0),
//                'minutes'   => (int) $node->filterXPath('.//td[@data-stat="mp"]')->text(0),
//                'points'    => (int) $node->filterXPath('.//td[@data-stat="pts"]')->text(0),
//                'rebounds'  => (int) $node->filterXPath('.//td[@data-stat="trb"]')->text(0),
//                'assists'   => (int) $node->filterXPath('.//td[@data-stat="ast"]')->text(0),
//                'steals'    => (int) $node->filterXPath('.//td[@data-stat="stl"]')->text(0),
//                'blocks'    => (int) $node->filterXPath('.//td[@data-stat="blk"]')->text(0),
//            ];

            $em->persist($player);
        });

        $em->flush();

        return new Response("Les joueurs ont été importés dans SQLite !");
    }
}
