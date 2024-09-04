<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\SearchType;
use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Form\AffectationType;
use App\Form\CollaborateurType;
use App\Form\SearchAffectationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AffectationRepository;
use App\Repository\CollaborateurRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/collaborateur')]
class CollaborateurController extends AbstractController
{
    /**
     * Liste des collaborateurs
     */
    #[Route('', name: 'collaborateur_index')]
    public function index(Request $request, CollaborateurRepository $collaborateurRepository, PaginatorInterface $paginator): Response
    {
        // On récupère le formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        // Si on a les filtres en paramètres, on les récupère (filtre en GET ou formulaire search)
        $search = ($form->isSubmitted() && $form->isValid()) ? $form->get('search')->getData() : "";
        $noAffectation = (!is_null($request->query->get("affected")) && $request->query->get("affected") == "false") ? true : false;

        // On récupère la requête pour afficher les collaborateurs (avec les filtres en paramètres)
        $queryBuilder = $collaborateurRepository->searchCollaborateurs($search,$noAffectation);

        // Utiliser KnpPaginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        return $this->render('collaborateur/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form
        ]);
    }

    /**
     * Affiche le détail d'un collaborateur
     */
    #[Route('/{id}/detail', name: 'collaborateur_detail')]
    public function detail(Collaborateur $collaborateur, AffectationRepository $affectationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // On récupère le formulaire de filtre d'affectations
        $form = $this->createForm(SearchAffectationType::class);
        $form->handleRequest($request);
        // Si on a le formulaire de filtre soumis, on récupère les valeurs
        $fonction = ($form->isSubmitted() && !is_null($form->get('fonction')->getData())) ? $form->get('fonction')->getData() : new Fonction;
        $date_affectation = ($form->isSubmitted() && !is_null($form->get('date')->getData())) ? $form->get('date')->getData() : null;

        // On récupère la requête pour afficher les affectations sur collaborateurs (avec les filtres en paramètres)
        $queryBuilder = $affectationRepository->searchAffectationsCollaborateur($collaborateur,$fonction,$date_affectation);

        // Utiliser KnpPaginator pour paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1)
        );
        
        return $this->render('collaborateur/detail.html.twig', [
            "collaborateur" => $collaborateur,
            "pagination" => $pagination,
            "formSearch" => $form
        ]);
    }

    /**
     * Gestion de la création d'un collaborateur
     */
    #[Route('/new', name: 'collaborateur_new')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire
        $collaborateur = new Collaborateur();
        $form = $this->createForm(CollaborateurType::class, $collaborateur);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On met les paramètres maquants par défaut
            $collaborateur->setAdmin(false);
            $collaborateur->setVerified(true);
            // On récupère la photo
            $image = $form->get('photo')->getData();
            // Si une photo est bien présente
            if ($image) {
                // On récupère le fichier
                $nomFichier = uniqid().'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory_collaborateur'),
                    $nomFichier
                );
                // On affecte la photo à l'utilisateur
                $collaborateur->setPhoto($nomFichier);
            }

            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($collaborateur);
            $em->flush();
            //On ajoute un message flash
            $this->addFlash('success','Le collaborateur a été enregistré avec succès');
            // Redirection vers la liste des collaborateurs
            return $this->redirectToRoute('collaborateur_index');
        }

        return $this->render('collaborateur/create.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Gestion de la modification d'un collaborateur
     */
    #[Route('/{id}/edit', name: 'collaborateur_edit')]
    public function edit(Collaborateur $collaborateur, Request $request, EntityManagerInterface $em, Filesystem $filesystem): Response
    {
        // Création du formulaire, chargé avec le collaborateur
        $form = $this->createForm(CollaborateurType::class, $collaborateur);
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
                    $this->getParameter('images_directory_collaborateur'),
                    $nomFichier
                );

                // On supprime l'ancienne photo
                $filesystem->remove($this->getParameter('images_directory_collaborateur').'/'.$collaborateur->getPhoto($nomFichier));

                // On attache la photo au collaborateur
                $collaborateur->setPhoto($nomFichier);
            }

            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($collaborateur);
            $em->flush();
            //On ajoute un message flash
            $this->addFlash('success','Le collaborateur a été modifié avec succès');
            // Redirection vers le détail de l'utilisateur
            return $this->redirectToRoute('collaborateur_detail',['id' => $collaborateur->getId()]);
        }

        return $this->render('collaborateur/edit.html.twig', [
            'form' => $form,
            'collaborateur' => $collaborateur
        ]);
    }

    /**
     * Suppression d'un collaborateur
     */
    #[Route('/{id}', name: 'collaborateur_delete', methods: ['DELETE'])]
    public function delete(Collaborateur $collaborateur, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // On récupère les données JSON
        $data = json_decode($request->getContent(), true);
        
        // On vérifie le token CSRF
        if ($this->isCsrfTokenValid('delete'.$collaborateur->getId(), $data['_token'])) {
            // On supprime le collaborateur
            $em->remove($collaborateur);
            $em->flush();

            //On ajoute un message flash
            $this->addFlash('success','Le collaborateur a été supprimé avec succès');

            // On créé le retour
            $response = [
                'status' => 'success',
                'redirect' => '/collaborateur'
            ];
        } else {
            //On ajoute un message flash
            $this->addFlash('error','Le collaborateur n\'a pas pu être supprimé');
            // On créé le retour
            $response = [
                'status' => 'error',
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Affecte un collaborateur
     */
    #[Route('/{id}/affectations/add', name: 'collaborateur_affected')]
    public function affected(Collaborateur $collaborateur, Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire
        $affectation = new Affectation();
        $form = $this->createForm(AffectationType::class,$affectation);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On définit l'utilisateur de l'affectation
            $affectation->setCollaborateur($collaborateur);
            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($affectation);
            $em->flush();
            //On ajoute un message flash
            $this->addFlash('success','Le collaborateur a été affecté au restaurant avec succès');
            // Redirection vers la page de détail du collaborateur
            return $this->redirectToRoute('collaborateur_detail',['id' => $collaborateur->getId()]);
        }

        return $this->render('collaborateur/affected.html.twig', [
            'form' => $form,
            'collaborateur' => $collaborateur
        ]);
    }

    /**
     * Modifie l'affectation d'un collaborateur
     */
    #[Route('/{collaborateur}/affectation/{affectation}/edit', name: 'collaborateur_affectation_edit')]
    public function affectationEdit(Collaborateur $collaborateur, Affectation $affectation, Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire
        $form = $this->createForm(AffectationType::class,$affectation);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $affectation->setCollaborateur($collaborateur);
            // Insertion en base de données
            // On utilise l'entity manager
            $em->persist($affectation);
            $em->flush();
            // On ajoute un message flash
            $this->addFlash('success','L\'affectation a été modifiée avec succès');
            // Redirection vers le détail du collaborateur
            return $this->redirectToRoute('collaborateur_detail',['id' => $collaborateur->getId()]);
        }

        return $this->render('collaborateur/edit.affectation.html.twig', [
            'form' => $form,
            'collaborateur' => $collaborateur
        ]);
    }
}
