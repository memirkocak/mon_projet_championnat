<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChampionshipController extends AbstractController
{
    #[Route('/championship/create', name: 'app_championship_create')]
    public function create(): Response
    {
        return $this->render('championship/create.html.twig');
    }
}

