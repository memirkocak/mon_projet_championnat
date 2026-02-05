<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameFormType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/game/create', name: 'app_game_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameFormType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que les deux équipes sont différentes
            if ($game->getTeam1() === $game->getTeam2()) {
                $this->addFlash('error', 'Les deux équipes doivent être différentes.');
                return $this->redirectToRoute('app_game_create');
            }

            try {
                $entityManager->persist($game);
                $entityManager->flush();
                $this->addFlash('success', 'Le résultat a été créé avec succès !');
                return $this->redirectToRoute('app_home');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
                return $this->redirectToRoute('app_game_create');
            }
        }

        return $this->render('game/create.html.twig', [
            'gameForm' => $form,
        ]);
    }

    #[Route('/games', name: 'app_games_list')]
    public function list(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findBy([], ['id' => 'DESC']);

        return $this->render('game/list.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/{id}/edit', name: 'app_game_edit')]
    public function edit(
        Request $request,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $game = $gameRepository->find($id);

        if (!$game) {
            throw $this->createNotFoundException('Match non trouvé');
        }

        $form = $this->createForm(GameFormType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que les deux équipes sont différentes
            if ($game->getTeam1() === $game->getTeam2()) {
                $this->addFlash('error', 'Les deux équipes doivent être différentes.');
                return $this->redirectToRoute('app_game_edit', ['id' => $id]);
            }

            try {
                $entityManager->flush();
                $this->addFlash('success', 'Le résultat a été modifié avec succès !');
                return $this->redirectToRoute('app_games_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
                return $this->redirectToRoute('app_game_edit', ['id' => $id]);
            }
        }

        return $this->render('game/edit.html.twig', [
            'gameForm' => $form,
            'game' => $game,
        ]);
    }

    #[Route('/game/{id}/delete', name: 'app_game_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $game = $gameRepository->find($id);

        if (!$game) {
            throw $this->createNotFoundException('Match non trouvé');
        }

        $dayId = $game->getDay()->getId();

        $entityManager->remove($game);
        $entityManager->flush();

        $this->addFlash('success', 'Le match a été supprimé avec succès !');
        
        // Rediriger vers la page de la journée si on vient de là, sinon vers la liste
        $referer = $request->headers->get('referer');
        if ($referer && strpos($referer, '/day/') !== false) {
            return $this->redirectToRoute('app_day_show', ['id' => $dayId]);
        }
        return $this->redirectToRoute('app_games_list');
    }
}

