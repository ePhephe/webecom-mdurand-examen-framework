<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\FonctionType;
use App\Repository\FonctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Générer avec make:crud
 */
#[Route('/fonction')]
class FonctionController extends AbstractController
{
    /**
     * Liste des fonctions possibles (postes)
     */
    #[Route('/', name: 'fonction_index', methods: ['GET'])]
    public function index(FonctionRepository $fonctionRepository): Response
    {
        return $this->render('fonction/index.html.twig', [
            'fonctions' => $fonctionRepository->findAll(),
        ]);
    }

    /**
     * Gestion de l'ajout d'une nouvelle fonction
     */
    #[Route('/new', name: 'fonction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fonction);
            $entityManager->flush();

            return $this->redirectToRoute('fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fonction/new.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
        ]);
    }

    /**
     * Détail d'une fonction
     */
    #[Route('/{id}', name: 'fonction_show', methods: ['GET'])]
    public function show(Fonction $fonction, FonctionRepository $fonctionRepository): Response
    {
        // On récupère la liste des affectations pour cette fonction
        $listAffectations = $fonctionRepository->findAffectations($fonction);

        return $this->render('fonction/show.html.twig', [
            'fonction' => $fonction,
            'affectations' => $listAffectations
        ]);
    }

    /**
     * Gestion de la modification d'une fonction
     */
    #[Route('/{id}/edit', name: 'fonction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fonction $fonction, FonctionRepository $fonctionRepository, EntityManagerInterface $entityManager): Response
    {
        // On récupère la liste des affectations pour cette fonction
        $listAffectations = $fonctionRepository->findAffectations($fonction);

        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fonction/edit.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
            'affectations' => $listAffectations
        ]);
    }

    /**
     * Suppression d'une fonction
     */
    #[Route('/{id}', name: 'fonction_delete', methods: ['POST'])]
    public function delete(Request $request, Fonction $fonction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fonction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fonction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fonction_index', [], Response::HTTP_SEE_OTHER);
    }
}
