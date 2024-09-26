<?php

namespace App\Controller;

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskOwner;
use App\Form\AddTaskType;
use App\Form\UpdateTaskType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\TaskOwnerRepository;
use App\Repository\TaskRepository;
use App\Repository\UsersRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;


class TaskController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EntityManagerInterface $entityManager,
    )
    {

    }
//        afficher une tache
//*****************************************************************************************
    #[Route('/task/{id}', name: 'show_task', requirements: ['id' => '\d+'])]
    public function showTask(int $id, TaskRepository $taskRepository, TaskOwnerRepository $taskOwnerRepository, UsersRepository $usersRepository, Request $request, EntityManagerInterface $entityManager ): Response
    {
        $usersTask = $taskOwnerRepository->findUsersByTaskId($id);
        $task = $taskRepository->find($id);
        $idProject = $taskOwnerRepository->findProjectIdByTaskId($id);

        $form = $this->createForm(UpdateTaskType::class, null, [
            'users' => $usersRepository->findAll(),
            'usersTask' => $usersTask,
            'task' => $task,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $task->setName($data['name']);
            $task -> setDescription($data['description']);
            $task -> setEndDate($data['endDate']);
            $task -> setStatus($data['status']);

            $entityManager->persist($task);
            $entityManager->flush();

            $selectedUsers = $form->get('employes')->getData();

            // Supprimer les anciens utilisateurs non sélectionnés
            foreach ($usersTask as $userTask) {
                if (!in_array($userTask->getId(), array_map(fn($user) => $user->getId(), $selectedUsers))) {
                    $taskUser = $taskOwnerRepository->findOneBy(['idTask' => $task->getId(), 'idUser' => $userTask->getId()]);
                    if ($taskUser) {
                        $entityManager->remove($taskUser);
                    }
                }
            }

            // Ajouter les nouveaux utilisateurs sélectionnés
            foreach ($selectedUsers as $user) {
                if (!in_array($user->getId(), array_map(fn($u) => $u->getId(), $usersTask))) {
                    $taskOwner = new TaskOwner();
                    $taskOwner->setIdTask($task->getId());
                    $taskOwner->setIdUser($user->getId());
                    $taskOwner->setIdProject($idProject);

                    $entityManager->persist($taskOwner);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully');

            return $this->redirectToRoute('show_project', ['id' => $idProject]);
        }

        return $this->render('task/task.html.twig', [
            'form' => $form->createView(),
            'idTask' => $id,
        ]);
    }
//*****************************************************************************************


//        Ajouter une Tache
//*****************************************************************************************
    #[Route('/task/add/{id}', name: 'add_task', requirements: ['id' => '\d+'])]
    public function addTask(int $id, UsersRepository $usersRepository, Request $request, EntityManagerInterface $entityManager, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, TaskOwnerRepository $taskOwnerRepository, TaskRepository $taskRepository)
    {
        $users = $usersRepository->findAll();

        $task = new Task();

        $form = $this->createForm(AddTaskType::class, null, [
            'users' => $users,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selectedUsers = $form->get('employes')->getData();

            $task->setName($data['name']);
            $task->setDescription($data['description']);
            $task->setEndDate($data['date']);
            $task->setStartDate(new \DateTime());
            $task->setStatus($data['status']);

            $entityManager->persist($task);
            $entityManager->flush();

            foreach ($selectedUsers as $user) {
                $taskOwner = new TaskOwner();
                $taskOwner->setIdTask($task->getId());
                $taskOwner->setIdProject($id);
                $taskOwner->setIdUser($user->getid());

                $entityManager->persist($taskOwner);
            }

            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully');

            return $this->redirectToRoute('show_project', ['id' => $id]);
        }


        return $this->render('task/taskAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
//*****************************************************************************************

//        supprimer une Tache
//*****************************************************************************************
    #[Route('/task/{id}/delete', name: 'delete_task', requirements: ['id' => '\d+'])]
    public function deleteTask(int $id, TaskRepository $taskRepository, TaskOwnerRepository $taskOwnerRepository, UsersRepository $usersRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $idProject = $taskOwnerRepository->findProjectIdByTaskId($id);

        $task = $taskRepository->find($id);
        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $entityManager->remove($task);

        $taskUsers = $taskOwnerRepository->findBy(['idTask' => $id]);

        foreach ($taskUsers as $taskUser) {
            $entityManager->remove($taskUser);
        }

        $entityManager->flush();

        return $this->redirectToRoute('show_project', ['id' => $idProject]);
    }
}
