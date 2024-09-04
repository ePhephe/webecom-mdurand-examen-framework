<?php

namespace App\Controller;

use App\Form\SearchAffectationType;
use App\Repository\AffectationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AffectationController extends AbstractController
{
    /**
     * Liste des affectations
     */
    #[Route('/affectation', name: 'affectation_index')]
    public function index(Request $request, AffectationRepository $affectationRepository, PaginatorInterface $paginator): Response
    {
        // On récupère le formulaire de recherche
        $form = $this->createForm(SearchAffectationType::class);
        $form->handleRequest($request);

        // Si le formulaire de filtre est soumis, on récupère les filtres sinon tableau vide
        $filtres = ($form->isSubmitted()) ? $form->getData() : [];

        // On récupère la requête pour avoir les affectations (avec les filtres en paramètres)
        $queryBuilder = $affectationRepository->searchAffectations($filtres);

        // Utiliser KnpPaginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        return $this->render('affectation/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form
        ]);
    }
}
