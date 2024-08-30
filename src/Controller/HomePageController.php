<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\ProjectRepository;
use App\Repository\UsersRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Project;
use App\Form\UpdateUser;

class HomePageController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private UsersRepository $UserRepository,
        private EntityManagerInterface $entityManager,
    )
    {

    }

    #[Route('/', name: 'app_homepage')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('homepage/projects.html.twig', [
            'controller_name' => 'HomePageController',
            'projects' => $projects,
        ]);
    }

    #[Route('/users', name: 'show_users')]
    public function showUsers(UsersRepository $UsersRepository): Response
    {
        $users = $UsersRepository->findAll();

        return $this->render('homepage/users.html.twig', [
            'controller_name' => 'HomePageController',
            'users' => $users,
        ]);
    }

    #[Route('/users/update/{id}', name: 'update_user', requirements: ['id' => '\d+'])]
    public function updateUsers(int $id, UsersRepository $UsersRepository, Request $request): Response
    {
        $user = $UsersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UpdateUser::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userUpdate = $form->getData();

            $this->entityManager->persist($userUpdate);
            $this->entityManager->flush();

            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('show_users'); // Redirect to a page showing the user list
        }

        return $this->render('homepage/updateUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users/{id}/delete', name: 'delete_user', requirements: ['id' => '\d+'])]
    public function delete(int $id, UsersRepository $UsersRepository): Response
    {
        $user = $UsersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $UsersRepository->remove($user, true);

        return $this->redirectToRoute('homepage/users.html.twig');
    }

    #[Route('/project/{id}', name: 'show_project', requirements: ['id' => '\d+'])]
    public function showProject(int $id, ProjectRepository $ProjectRepository): Response
    {
        $project = $ProjectRepository->find($id);

        return $this->render('homepage/project.html.twig', [
            'controller_name' => 'HomePageController',
            'project' => $project,
        ]);
    }

}
