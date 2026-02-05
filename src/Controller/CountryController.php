<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryFormType;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CountryController extends AbstractController
{
    #[Route('/country/create', name: 'app_country_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $country = new Country();
        $form = $this->createForm(CountryFormType::class, $country);
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
                    return $this->render('country/create.html.twig', [
                        'countryForm' => $form,
                    ]);
                }

                $country->setLogo($newFilename);
            }

            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('success', 'Le pays a été créé avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('country/create.html.twig', [
            'countryForm' => $form,
        ]);
    }

    #[Route('/countries', name: 'app_countries_list')]
    public function list(CountryRepository $countryRepository): Response
    {
        $countries = $countryRepository->findBy([], ['name' => 'ASC']);

        return $this->render('country/list.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('/country/{id}/edit', name: 'app_country_edit')]
    public function edit(
        Request $request,
        CountryRepository $countryRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        int $id
    ): Response {
        $country = $countryRepository->find($id);

        if (!$country) {
            throw $this->createNotFoundException('Pays non trouvé');
        }

        $oldLogo = $country->getLogo();
        $form = $this->createForm(CountryFormType::class, $country);
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
                    $country->setLogo($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du logo.');
                    return $this->render('country/edit.html.twig', [
                        'countryForm' => $form,
                        'country' => $country,
                    ]);
                }
            } else {
                // Garder l'ancien logo si aucun nouveau fichier n'est fourni
                $country->setLogo($oldLogo);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le pays a été modifié avec succès !');
            return $this->redirectToRoute('app_countries_list');
        }

        return $this->render('country/edit.html.twig', [
            'countryForm' => $form,
            'country' => $country,
        ]);
    }

    #[Route('/country/{id}/delete', name: 'app_country_delete', methods: ['POST'])]
    public function delete(
        CountryRepository $countryRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $country = $countryRepository->find($id);

        if (!$country) {
            throw $this->createNotFoundException('Pays non trouvé');
        }

        // Vérifier s'il y a des équipes liées
        if ($country->getTeams()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce pays car il est associé à ' . $country->getTeams()->count() . ' équipe(s). Veuillez d\'abord supprimer ou modifier les équipes associées.');
            return $this->redirectToRoute('app_countries_list');
        }

        // Supprimer le logo s'il existe
        if ($country->getLogo()) {
            $logoPath = $this->getParameter('logos_directory') . '/' . $country->getLogo();
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        $entityManager->remove($country);
        $entityManager->flush();

        $this->addFlash('success', 'Le pays a été supprimé avec succès !');
        return $this->redirectToRoute('app_countries_list');
    }
}

