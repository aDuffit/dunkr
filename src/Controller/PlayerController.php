<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/player')]
final class PlayerController extends AbstractController
{
    #[Route(name: 'app_player_ranking', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('player/ranking.html.twig');
    }

    #[Route('/compare', name: 'app_player_compare', methods: ['GET'])]
    public function compare(): Response
    {
        return $this->render('player/comparison.html.twig');
    }

    #[Route('/search', name: 'app_player_search', methods: ['GET'])]
    public function search(): Response
    {
        return $this->render('player/search.html.twig');
    }
}
