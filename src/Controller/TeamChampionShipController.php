<?php

namespace App\Controller;

use App\Entity\TeamChampionShip;
use App\Form\TeamChampionShipFormType;
use App\Repository\TeamChampionShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamChampionShipController extends AbstractController
{
    #[Route('/team-championship/create', name: 'app_team_championship_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $teamChampionShip = new TeamChampionShip();
        $form = $this->createForm(TeamChampionShipFormType::class, $teamChampionShip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si cette association existe déjà
            $existing = $entityManager->getRepository(TeamChampionShip::class)
                ->findOneBy([
                    'championship' => $teamChampionShip->getChampionship(),
                    'team' => $teamChampionShip->getTeam()
                ]);

            if ($existing) {
                $this->addFlash('error', 'Cette équipe participe déjà à ce championnat.');
                return $this->redirectToRoute('app_team_championship_create');
            }

            $entityManager->persist($teamChampionShip);
            $entityManager->flush();

            $this->addFlash('success', 'L\'équipe a été associée au championnat avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('team_championship/create.html.twig', [
            'teamChampionShipForm' => $form,
        ]);
    }
}

