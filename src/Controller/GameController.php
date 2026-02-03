<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/game/create', name: 'app_game_create')]
    public function create(): Response
    {
        return $this->render('game/create.html.twig');
    }
}

