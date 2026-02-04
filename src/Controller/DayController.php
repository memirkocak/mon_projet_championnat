<?php

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DayController extends AbstractController
{
    #[Route('/day/create', name: 'app_day_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $day = new Day();
        $form = $this->createForm(DayFormType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($day);
            $entityManager->flush();

            $this->addFlash('success', 'La journée a été créée avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('day/create.html.twig', [
            'dayForm' => $form,
        ]);
    }
}

