<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    #[Route('/team/create', name: 'app_team_create')]
    public function create(): Response
    {
        return $this->render('team/create.html.twig');
    }
}

