<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_loginPage', methods: ['GET', 'POST'])]
    public function loginPage(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'email' => $email,
            'error' => $error,
        ]);
    }

    #[Route('/not-authorized', name: 'not_authorized')]
    public function notAuthorized(): Response
    {
        return $this->render('security/not_authorized.html.twig', [
            'message' => 'Vous n\'êtes pas autorisé à accéder à cette page.',
        ]);
    }
}
