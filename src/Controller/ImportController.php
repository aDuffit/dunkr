<?php

namespace App\Controller;

use App\Entity\Prospect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/import')]
final class ImportController extends AbstractController
{
    #[Route('/prospects', name: 'app_import_prospects', methods: ['GET'])]
    public function import(EntityManagerInterface $em, HttpClientInterface $client): Response
    {
        $response = $client->request('GET', 'https://basketball.realgm.com/international/league/1/Euroleague/stats');
        $crawler = new Crawler($response->getContent());

        // 2. Créer le dictionnaire des colonnes par TEXTE
        $columnMap = [];
        $crawler->filterXPath('//thead/tr/th')->each(function (Crawler $node, $i) use (&$columnMap) {
            $title = trim($node->text());
            if ($title) {
                $columnMap[$title] = $i + 1; // XPath commence à 1
            }
        });

        // 3. Importer les lignes
        $crawler->filterXPath('//tbody/tr')->each(function (Crawler $node) use ($columnMap, $em) {
            $prospect = new Prospect();
            $prospect->setName($node->filterXPath('//td[' . $columnMap['Player'] . ']')->text());
            $prospect->setPointsAvg((float) $node->filterXPath('//td[' . $columnMap['PPG'] . ']')->text());
            $prospect->setReboundsAvg((float) $node->filterXPath('//td[' . $columnMap['RPG'] . ']')->text());
            $prospect->setAssistsAvg((float) $node->filterXPath('//td[' . $columnMap['APG'] . ']')->text());

            $em->persist($prospect);
        });

        $em->flush();

        return new Response("Les prospects ont été importés dans SQLite !");
    }
}
