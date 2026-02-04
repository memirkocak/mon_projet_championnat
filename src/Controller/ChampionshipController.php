<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Form\ChampionshipFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChampionshipController extends AbstractController
{
    #[Route('/championship/create', name: 'app_championship_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $championship = new Championship();
        $form = $this->createForm(ChampionshipFormType::class, $championship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($championship);
            $entityManager->flush();

            $this->addFlash('success', 'Le championnat a été créé avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('championship/create.html.twig', [
            'championshipForm' => $form,
        ]);
    }
}

