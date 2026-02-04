<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameFormType;
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
}

