<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamFormType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TeamController extends AbstractController
{
    #[Route('/team/create', name: 'app_team_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $team = new Team();
        $form = $this->createForm(TeamFormType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                try {
                    $logoFile->move(
                        $this->getParameter('logos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du logo.');
                    return $this->render('team/create.html.twig', [
                        'teamForm' => $form,
                    ]);
                }

                $team->setLogo($newFilename);
            }

            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', 'L\'équipe a été créée avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('team/create.html.twig', [
            'teamForm' => $form,
        ]);
    }

    #[Route('/teams', name: 'app_teams_list')]
    public function list(TeamRepository $teamRepository): Response
    {
        $teams = $teamRepository->findBy([], ['name' => 'ASC']);

        return $this->render('team/list.html.twig', [
            'teams' => $teams,
        ]);
    }

    #[Route('/team/{id}/edit', name: 'app_team_edit')]
    public function edit(
        Request $request,
        TeamRepository $teamRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        int $id
    ): Response {
        $team = $teamRepository->find($id);

        if (!$team) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }

        $oldLogo = $team->getLogo();
        $form = $this->createForm(TeamFormType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                // Supprimer l'ancien logo s'il existe
                if ($oldLogo) {
                    $oldLogoPath = $this->getParameter('logos_directory') . '/' . $oldLogo;
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }

                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                try {
                    $logoFile->move(
                        $this->getParameter('logos_directory'),
                        $newFilename
                    );
                    $team->setLogo($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du logo.');
                    return $this->render('team/edit.html.twig', [
                        'teamForm' => $form,
                        'team' => $team,
                    ]);
                }
            } else {
                // Garder l'ancien logo si aucun nouveau fichier n'est fourni
                $team->setLogo($oldLogo);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'équipe a été modifiée avec succès !');
            return $this->redirectToRoute('app_teams_list');
        }

        return $this->render('team/edit.html.twig', [
            'teamForm' => $form,
            'team' => $team,
        ]);
    }
}

