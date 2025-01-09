<?php
namespace App\Controller;

use App\Form\RegistrationType;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registrationPage')]
    public function registrationPage(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();

        $form = $this->createForm(RegistrationType::class, $user);
        if ($user->getStartDateContract() === null) {
            $user->setStartDateContract(\DateTime::createFromFormat('d-m-Y', date('d-m-Y')));
            $user->setContract('CDI');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $formData->getPassword()
            );

            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('auth/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
