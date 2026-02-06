<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Entity\Country;
use App\Form\ChampionshipFormType;
use App\Repository\ChampionshipRepository;
use App\Repository\CountryRepository;
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

    #[Route('/championships', name: 'app_championships_list')]
    public function list(
        Request $request,
        ChampionshipRepository $championshipRepository,
        CountryRepository $countryRepository
    ): Response {
        $countryId = $request->query->get('country');
        $selectedCountry = null;
        $championships = [];

        if ($countryId) {
            $selectedCountry = $countryRepository->find($countryId);
        }

        $championships = $championshipRepository->findByCountry($selectedCountry);
        $countries = $countryRepository->findBy([], ['name' => 'ASC']);

        return $this->render('championship/list.html.twig', [
            'championships' => $championships,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry,
        ]);
    }

    #[Route('/championship/{id}', name: 'app_championship_show')]
    public function show(ChampionshipRepository $championshipRepository, int $id): Response
    {
        $championship = $championshipRepository->find($id);

        if (!$championship) {
            throw $this->createNotFoundException('Championnat non trouvé');
        }

        $teams = [];
        $teamPoints = [];
        
        foreach ($championship->getDays() as $day) {
            foreach ($day->getGames() as $game) {
                $team1 = $game->getTeam1();
                $team2 = $game->getTeam2();
                $team1Id = $team1->getId();
                $team2Id = $team2->getId();
                
                if (!isset($teams[$team1Id])) {
                    $teams[$team1Id] = $team1;
                    $teamPoints[$team1Id] = 0;
                }
                if (!isset($teams[$team2Id])) {
                    $teams[$team2Id] = $team2;
                    $teamPoints[$team2Id] = 0;
                }
                
                $team1Points = $game->getTeam1Point();
                $team2Points = $game->getTeam2Point();
                
                if ($team1Points > $team2Points) {
                    $teamPoints[$team1Id] += $championship->getWonPoint();
                    $teamPoints[$team2Id] += $championship->getLostPoint();
                } elseif ($team2Points > $team1Points) {
                    $teamPoints[$team1Id] += $championship->getLostPoint();
                    $teamPoints[$team2Id] += $championship->getWonPoint();
                } else {
                    $teamPoints[$team1Id] += $championship->getDrawPoint();
                    $teamPoints[$team2Id] += $championship->getDrawPoint();
                }
            }
        }
        
        $ranking = [];
        foreach ($teams as $teamId => $team) {
            $ranking[] = [
                'team' => $team,
                'points' => $teamPoints[$teamId] ?? 0
            ];
        }
        
        usort($ranking, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });

        return $this->render('championship/show.html.twig', [
            'championship' => $championship,
            'teams' => array_values($teams),
            'ranking' => $ranking,
        ]);
    }


    #[Route('/championship/{id}/edit', name: 'app_championship_edit')]
    public function edit(
        Request $request,
        ChampionshipRepository $championshipRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $championship = $championshipRepository->find($id);

        if (!$championship) {
            throw $this->createNotFoundException('Championnat non trouvé');
        }

        $form = $this->createForm(ChampionshipFormType::class, $championship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le championnat a été modifié avec succès !');
            return $this->redirectToRoute('app_championships_list');
        }

        return $this->render('championship/edit.html.twig', [
            'championshipForm' => $form,
            'championship' => $championship,
        ]);
    }

    #[Route('/championship/{id}/delete', name: 'app_championship_delete', methods: ['POST'])]
    public function delete(
        ChampionshipRepository $championshipRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $championship = $championshipRepository->find($id);

        if (!$championship) {
            throw $this->createNotFoundException('Championnat non trouvé');
        }

        // Les jours et matchs seront supprimés en cascade grâce à la configuration cascade: ['persist', 'remove']
        $entityManager->remove($championship);
        $entityManager->flush();

        $this->addFlash('success', 'Le championnat a été supprimé avec succès !');
        return $this->redirectToRoute('app_championships_list');
    }
}

