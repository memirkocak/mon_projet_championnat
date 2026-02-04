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
        foreach ($championship->getDays() as $day) {
            foreach ($day->getGames() as $game) {
                $team1Id = $game->getTeam1()->getId();
                $team2Id = $game->getTeam2()->getId();
                
                if (!isset($teams[$team1Id])) {
                    $teams[$team1Id] = $game->getTeam1();
                }
                if (!isset($teams[$team2Id])) {
                    $teams[$team2Id] = $game->getTeam2();
                }
            }
        }

        return $this->render('championship/show.html.twig', [
            'championship' => $championship,
            'teams' => array_values($teams),
        ]);
    }
}

