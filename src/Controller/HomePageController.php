<?php

namespace App\Controller;

use App\Repository\ProjectRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class HomePageController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,

    )
    {

    }

    /**
     * Afficher la homepage
     */
    #[Route('/', name: 'app_homepage')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('homepage/projects.html.twig', [
            'controller_name' => 'HomePageController',
            'projects' => $projects,
        ]);
    }
}
