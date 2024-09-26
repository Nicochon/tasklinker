<?php

namespace App\Controller;

namespace App\Controller;

use App\Repository\UsersRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Form\UpdateUserType;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {

    }

    /**
     * Afficher les utilisateurs
     */
    #[Route('/users', name: 'show_users')]
    public function showUsers(UsersRepository $UsersRepository): Response
    {
        $users = $UsersRepository->findAll();

        return $this->render('user/users.html.twig', [
            'controller_name' => 'HomePageController',
            'users' => $users,
        ]);
    }

    /**
     * Mettre à jour les utilisateurs
     */
    #[Route('/users/update/{id}', name: 'update_user', requirements: ['id' => '\d+'])]
    public function updateUsers(int $id, UsersRepository $UsersRepository, Request $request): Response
    {
        $user = $UsersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userUpdate = $form->getData();

            $this->entityManager->persist($userUpdate);
            $this->entityManager->flush();

            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('show_users'); // Redirect to a page showing the user list
        }

        return $this->render('user/updateUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    #[Route('/users/{id}/delete', name: 'delete_user', requirements: ['id' => '\d+'])]
    public function delete(int $id, UsersRepository $UsersRepository): Response
    {
        $user = $UsersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $UsersRepository->remove($user, true);

        return $this->redirectToRoute('show_users');
    }
}
