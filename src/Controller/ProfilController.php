<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    /**
     * Gestion de la mise à jour du profil de l'utilisateur connecté
     */
    #[Route('/profil', name: 'profil_index')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser();
        // Création du formulaire
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et est valide
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
                $filesystem->remove($this->getParameter('images_directory_collaborateur').'/'.$user->getPhoto($nomFichier));

                // On affecte la photo à l'utilisateur
                $user->setPhoto($nomFichier);
            }

            // Si le mot de passe a été rempli
            if(!empty($form->get('plainPassword')->getData())){
                // On le définit comme le nouveau mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            // On flag si l'email à changer pour envoyer la vérification une fois mis à jour
            $flagChangedEmail = ($form->get('email')->getData() != $user->getEmail()) ? true : false;

            // On met à jours l'utilisateur en bdd
            $entityManager->persist($user);
            $entityManager->flush();

            // Si le flag de changement d'email est vrai, on envoie un email de vérification
            if($flagChangedEmail) {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('noreply@worldofmddev.fr', 'NoReply'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
            }

            //On ajoute un message flash
            $this->addFlash('success','Votre profil a été mis à jour avec succès !');
        }

        return $this->render('profil/index.html.twig', [
            'form' => $form,
        ]);
    }
}
