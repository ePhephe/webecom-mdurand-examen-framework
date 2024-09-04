<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Page de login
     */
    #[Route(path: '', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // @var User $user
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Déconnexion
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Vérification si l'utilisateur peut se connecter
     *
     * @return Response
     */
    #[Route(path: '/check_user_status', name: 'app_check_user_status')]
    public function checkUserStatus(): Response
    {
        // @var User $user
        $user = $this->getUser();

        if (!$user || !$user->isadmin()) {
            return $this->redirectToRoute('app_logout');
        }

        // Redirigez l'utilisateur vers la page souhaitée
        return $this->redirectToRoute('app_accueil');
    }
}
