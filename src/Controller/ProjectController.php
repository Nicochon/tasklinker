<?php

namespace App\Controller;

use App\Entity\ProjectUser;
use App\Form\AddProjectType;
use App\Form\UpdateProjectType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\TaskOwnerRepository;
use App\Repository\TaskRepository;
use App\Repository\UsersRepository;

use App\Service\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Project;

class ProjectController extends AbstractController
{

    public function __construct(
        private ProjectRepository $projectRepository,
        private ProjectService $projectService,
        private EntityManagerInterface $entityManager,
    )
    {

    }
//       Afficher un Project
//*****************************************************************************************
    #[Route('/project/{id}', name: 'show_project', requirements: ['id' => '\d+'])]
    public function showProject(int $id, ProjectRepository $projectRepository): Response
    {
        $project = $projectRepository->find($id);

        $initials = $this->projectService->getInitialsByProjectId($id);
        $tasks = $this->projectService->getTasksByProjectId($id);
        $taskInitials = $this->projectService->getUsersByTask($tasks);
        //*********************************************************************************

        return $this->render('project/project.html.twig', [
            'controller_name' => 'HomePageController',
            'project' => $project,
            'initials' => $initials,
            'tasks' => $tasks,
            'taskInitials' => $taskInitials,
        ]);
    }

//*****************************************************************************************

//        Ajouter un Project
//*****************************************************************************************
    #[Route('/project/add', name: 'add_project')]
    public function addProject(UsersRepository $usersRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $users = $usersRepository->findAll();

        $project = new Project();

        $form = $this->createForm(AddProjectType::class, null, [
            'users' => $users,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $name = $data['name'];
            $selectedUsers = $form->get('employes')->getData();

            $project->setName($name);

            $entityManager->persist($project);
            $entityManager->flush();

            foreach ($selectedUsers as $user) {
                $projectUser = new ProjectUser();
                $projectUser->setIdProject($project->getId());
                $projectUser->setIdUser($user->getid());

                $entityManager->persist($projectUser);
            }

            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('project/projectAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
//*****************************************************************************************


//        Mise a jour d'un Project
//*****************************************************************************************
    #[Route('/project/update/{id}', name: 'update_project', requirements: ['id' => '\d+'])]
    public function updateProject(int $id, UsersRepository $usersRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $usersProjectId = $projectUserRepository->findUserIdsByProjectId($id);
        $usersProject = [];

        $project = $projectRepository->find($id);

        foreach ($usersProjectId as $user){
            $usersProject[] = $usersRepository->find($user['idUser']);
        }

        $form = $this->createForm(UpdateProjectType::class, null, [
            'users' => $usersRepository->findAll(),
            'usersProject' => $usersProject,
            'project' => $project,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $name = $data['name'];

            $project->setName($name);

            $entityManager->persist($project);
            $entityManager->flush();

            $selectedUsers = $form->get('employes')->getData();

            // Supprimer les anciens utilisateurs non sélectionnés
            foreach ($usersProjectId as $userProject) {
                if (!in_array($userProject['idUser'], array_map(fn($user) => $user->getId(), $selectedUsers))) {
                    $projectUser = $projectUserRepository->findOneBy(['idProject' => $project->getId(), 'idUser' => $userProject['idUser']]);
                    if ($projectUser) {
                        $entityManager->remove($projectUser);
                    }
                }
            }

            // Ajouter les nouveaux utilisateurs sélectionnés
            foreach ($selectedUsers as $user) {
                if (!in_array($user->getId(), array_map(fn($u) => $u['idUser'], $usersProjectId))) {
                    $projectUser = new ProjectUser();
                    $projectUser->setIdProject($project->getId());
                    $projectUser->setIdUser($user->getId());

                    $entityManager->persist($projectUser);
                }
            }
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('project/updateProject.html.twig', [
            'form' => $form->createView(),
        ]);
    }
//*****************************************************************************************


//        supprimer un Project
//*****************************************************************************************
    #[Route('/project/{id}/delete', name: 'delete_project', requirements: ['id' => '\d+'])]
    public function deleteProject(int $id, ProjectUserRepository $projectUserRepository, EntityManagerInterface $entityManager, ProjectRepository $projectRepository, TaskRepository $taskRepository, TaskOwnerRepository $taskOwnerRepository): Response
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }
        $entityManager->remove($project);

        $tasksId = $taskOwnerRepository->findTaskIdsByProjectId($id);
        foreach ($tasksId as $taskId) {
            $task = $taskRepository->find($taskId['idTask']);
            $taskOwners = $taskOwnerRepository->findBy(['idTask' => $task->getId()]);

            foreach ($taskOwners as $taskOwner) {
                $entityManager->remove($taskOwner);
            }

            $entityManager->remove($task);
        }

        $projectUsers = $projectUserRepository->findBy(['idProject' => $id]);
        foreach ($projectUsers as $projectUser) {
            $entityManager->remove($projectUser);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_homepage');
    }
//*****************************************************************************************

}
