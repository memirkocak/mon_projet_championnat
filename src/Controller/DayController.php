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
            return $this->redirectToRoute('app_championship_show', ['id' => $day->getChampionship()->getId()]);
        }

        return $this->render('day/create.html.twig', [
            'dayForm' => $form,
        ]);
    }

    #[Route('/day/{id}/edit', name: 'app_day_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        DayRepository $dayRepository,
        EntityManagerInterface $entityManager,
        string $id
    ): Response {
        $day = $dayRepository->find((int) $id);

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

    #[Route('/day/{id}', name: 'app_day_show', requirements: ['id' => '\d+'])]
    public function show(DayRepository $dayRepository, string $id): Response
    {
        $day = $dayRepository->find((int) $id);

        if (!$day) {
            throw $this->createNotFoundException('Journée non trouvée');
        }

        $championship = $day->getChampionship();
        $games = $day->getGames();

        // Calculer les statistiques pour chaque match
        $gameStats = [];
        foreach ($games as $game) {
            $team1Points = $game->getTeam1Point();
            $team2Points = $game->getTeam2Point();
            
            // Déterminer le résultat et les points gagnés
            $team1Result = 'draw';
            $team2Result = 'draw';
            $team1PointsEarned = $championship->getDrawPoint();
            $team2PointsEarned = $championship->getDrawPoint();
            
            if ($team1Points > $team2Points) {
                $team1Result = 'win';
                $team2Result = 'loss';
                $team1PointsEarned = $championship->getWonPoint();
                $team2PointsEarned = $championship->getLostPoint();
            } elseif ($team2Points > $team1Points) {
                $team1Result = 'loss';
                $team2Result = 'win';
                $team1PointsEarned = $championship->getLostPoint();
                $team2PointsEarned = $championship->getWonPoint();
            }
            
            $gameStats[] = [
                'game' => $game,
                'team1Result' => $team1Result,
                'team2Result' => $team2Result,
                'team1PointsEarned' => $team1PointsEarned,
                'team2PointsEarned' => $team2PointsEarned,
            ];
        }

        return $this->render('day/show.html.twig', [
            'day' => $day,
            'championship' => $championship,
            'gameStats' => $gameStats,
        ]);
    }

    #[Route('/day/{id}/delete', name: 'app_day_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        DayRepository $dayRepository,
        EntityManagerInterface $entityManager,
        string $id
    ): Response {
        $day = $dayRepository->find((int) $id);

        if (!$day) {
            throw $this->createNotFoundException('Journée non trouvée');
        }

        $championshipId = $day->getChampionship()->getId();

        // Les matchs seront supprimés en cascade grâce à la configuration cascade: ['persist', 'remove']
        $entityManager->remove($day);
        $entityManager->flush();

        $this->addFlash('success', 'La journée a été supprimée avec succès !');
        return $this->redirectToRoute('app_championship_show', ['id' => $championshipId]);
    }
}

