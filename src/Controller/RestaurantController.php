<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Entity\Restaurant;
use App\Entity\Affectation;
use App\Form\RestaurantType;
use App\Form\AffectationType;
use App\Form\SearchAffectationType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    /**
     * Liste des restaurants (page d'accueil)
     */
    #[Route('', name: 'app_accueil')]
    public function index(Request $request, RestaurantRepository $restaurantRepository, PaginatorInterface $paginator): Response
    {
        // On récupère la requête pour lister les restaurants 
        $queryBuilder = $restaurantRepository->createQueryBuilder('r')->orderBy('r.id','DESC');

        // On récupère le formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Si le formulaire de recherche est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            // On ajoute les paramètres de recherche à la requête
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('UPPER(r.nom)', ':search'),
                    $queryBuilder->expr()->like('UPPER(r.ville)', ':search'),
                    $queryBuilder->expr()->like('UPPER(r.code_postal)', ':search')
                )
            )
            ->setParameter(':search','%'.strtoupper($form->get('search')->getData()).'%');
        }

        // Utiliser KnpPaginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        return $this->render('restaurant/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form
        ]);
    }

    /**
     * Affiche le détail d'un restaurant
     */
    #[Route('/{id}/detail', name: 'restaurant_detail')]
    public function detail(Restaurant $restaurant, Request $request, RestaurantRepository $restaurantRepository, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        // On récupère le formulaire de recherche des affectations
        $form = $this->createForm(SearchAffectationType::class);
        $form->handleRequest($request);

        // Si le formulaire de recherche des affectations est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            // On récupère les paramètres
            $search = $form->get('search')->getData();
            $fonction = $form->get('fonction')->getData();
            $date_affectation = $form->get('date')->getData();
        }
        else {
            $search = "";
            $fonction = "";
            $date_affectation = "";
        }

        // On récupère la requête pour voir les affectations du restaurant
        $queryBuilder = $restaurantRepository->searchAffectations($restaurant, $search, $fonction, $date_affectation, $em);

        // Utiliser KnpPaginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        return $this->render('restaurant/detail.html.twig', [
            "restaurant" => $restaurant,
            "pagination" => $pagination,
            "formSearch" => $form
        ]);
    }

    /**
     * Gestion de la création d'un restaurant
     */
    #[Route('/new', name: 'restaurant_new')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère la photo
            $image = $form->get('photo')->getData();
            // Si une photo est bien présente
            if ($image) {
                // On récupère le fichier
                $nomFichier = uniqid().'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory_restaurant'),
                    $nomFichier
                );
                // On attache la photo au restaurant
                $restaurant->setPhoto($nomFichier);
            }

            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($restaurant);
            $em->flush();
            //On ajoute un message flash
            $this->addFlash('success','Le restaurant a été enregistré avec succès');
            // Redirection vers la liste des restaurants (accueil)
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('restaurant/create.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Gestion de la modification d'un restaurant
     */
    #[Route('/{id}/edit', name: 'restaurant_edit')]
    public function edit(Restaurant $restaurant, Request $request, EntityManagerInterface $em, Filesystem $filesystem, RestaurantRepository $restaurantRepository, PaginatorInterface $paginator): Response
    {
        // On récupère le formulaire de recherche des affectations
        $formSearch = $this->createForm(SearchAffectationType::class);
        $formSearch->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formSearch->isSubmitted() && $formSearch->isValid()) {
            // On récupère les valeurs
            $search = $formSearch->get('search')->getData();
            $fonction = $formSearch->get('fonction')->getData();
            $date_affectation = $formSearch->get('date')->getData();
        }
        else {
            $search = "";
            $fonction = "";
            $date_affectation = "";
        }

        // On récupère la requête des affectations pour le restaurant
        $queryBuilder = $restaurantRepository->searchAffectations($restaurant, $search, $fonction, $date_affectation, $em, true);

        // Utiliser KnpPaginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        // Création du formulaire
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est balide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère la photo
            $image = $form->get('photo')->getData();
            // Si une photo est bien présente
            if ($image) {
                // On récupère le fichier
                $nomFichier = uniqid().'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory_restaurant'),
                    $nomFichier
                );

                // On supprime l'ancienne photo
                $filesystem->remove($this->getParameter('images_directory_article').'/'.$restaurant->getPhoto($nomFichier));

                // On attache la photo au restaurant
                $restaurant->setPhoto($nomFichier);
            }

            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($restaurant);
            $em->flush();
            //On ajoute un message flash
            $this->addFlash('success','Le restaurant a été modifié avec succès');
            // Redirection vers la liste des restaurants
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('restaurant/edit.html.twig', [
            'form' => $form,
            'formSearch' => $formSearch,
            'restaurant' => $restaurant,
            "pagination" => $pagination,
        ]);
    }

    /**
     * Suppression d'un restaurant
     */
    #[Route('/{id}', name: 'restaurant_delete', methods: ['DELETE'])]
    public function delete(Restaurant $restaurant, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // On récupère les données JSON
        $data = json_decode($request->getContent(), true);

        // On vérifie le token CSRF
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $data['_token'])) {
            // On supprime le restaurant
            $em->remove($restaurant);
            $em->flush();

            //On ajoute un message flash
            $this->addFlash('success','Le restaurant a été supprimé avec succès');

            // On créé le retour
            $response = [
                'status' => 'success',
                'redirect' => '/restaurant'
            ];
        } else {
            //On ajoute un message flash
            $this->addFlash('error','Le restaurant n\'a pas pu être supprimé');
            // On créé le retour
            $response = [
                'status' => 'error',
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Affecter un collaborateur dans un restaurant
     */
    #[Route('/{id}/affectations/add', name: 'restaurant_affected')]
    public function affected(Restaurant $restaurant, Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire
        $affectation = new Affectation();
        $form = $this->createForm(AffectationType::class,$affectation);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On définit le restaurant de l'affectation
            $affectation->setRestaurant($restaurant);
            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($affectation);
            $em->flush();
            //On ajoute un message flash
            $this->addFlash('success','Le collaborateur a été affecté au restaurant avec succès');
            // Redirection vers la modification du restaurant
            return $this->redirectToRoute('restaurant_edit',['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/affected.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant
        ]);
    }
}
