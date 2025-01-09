<?php

namespace App\Controller;

use App\Repository\ProjectRepository;

use App\Repository\ProjectUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class HomePageController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository,)
    {

    }

    #[Route('/home', name: 'app_home')]
    public function index(ProjectRepository $projectRepository, ProjectUserRepository $projectUserRepository,  Security $security): Response
    {
        $user = $security->getUser();
        $projects = [];
        if ($user && in_array('ROLE_USER', $user->getRoles())) {
            $projectsId = $projectUserRepository->findProjectsByUser($user);

            foreach ($projectsId as $projectId) {
                $project = $projectRepository->getProjectById($projectId['idProject']);
                $projects[] = $project;
            }
        } else {
            $projects = $projectRepository->findAll();
        }

        return $this->render('homepage/projects.html.twig', [
            'controller_name' => 'HomePageController',
            'projects' => $projects,
        ]);
    }

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function displayConnectionPage(): Response
    {
        return $this->render('auth/dispatch-page.html.twig');

    }
}
