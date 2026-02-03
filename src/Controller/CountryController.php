<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CountryController extends AbstractController
{
    #[Route('/country/create', name: 'app_country_create')]
    public function create(): Response
    {
        return $this->render('country/create.html.twig');
    }
}

