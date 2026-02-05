<?php

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayFormType;
use App\Repository\DayRepository;
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

    #[Route('/day/{id}/edit', name: 'app_day_edit')]
    public function edit(
        Request $request,
        DayRepository $dayRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $day = $dayRepository->find($id);

        if (!$day) {
            throw $this->createNotFoundException('Journée non trouvée');
        }

        $form = $this->createForm(DayFormType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La journée a été modifiée avec succès !');
            return $this->redirectToRoute('app_championship_show', ['id' => $day->getChampionship()->getId()]);
        }

        return $this->render('day/edit.html.twig', [
            'dayForm' => $form,
            'day' => $day,
        ]);
    }
}

