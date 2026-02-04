<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamFormType;
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
}

