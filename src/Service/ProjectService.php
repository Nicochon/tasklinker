<?php

namespace App\Service;

use App\Repository\ProjectUserRepository;
use App\Repository\UsersRepository;
use App\Repository\TaskOwnerRepository;
use App\Repository\TaskRepository;

class ProjectService
{
    private $projectUserRepository;
    private $usersRepository;
    private $taskOwnerRepository;
    private $taskRepository;

    public function __construct(
        ProjectUserRepository $projectUserRepository,
        UsersRepository $usersRepository,
        TaskOwnerRepository $taskOwnerRepository,
        TaskRepository $taskRepository
    ) {
        $this->projectUserRepository = $projectUserRepository;
        $this->usersRepository = $usersRepository;
        $this->taskOwnerRepository = $taskOwnerRepository;
        $this->taskRepository = $taskRepository;
    }

    public function getInitialsByProjectId(int $idProject): array
    {
        $userIds = $this->projectUserRepository->findUserIdsByProjectId($idProject);
        $userIdsArray = array_column($userIds, 'idUser');
        $users = $this->usersRepository->findBy(['id' => $userIdsArray]);

        $initials = [];
        foreach ($users as $user) {
            $initials[] = $user->getInitials();
        }

        return $initials;
    }

    public function getTasksByProjectId(int $idProject): array
    {
        $taskId = $this->taskOwnerRepository->findTaskIdsByProjectId($idProject);
        $taskIdsArray = array_column($taskId, 'idTask');
        return $this->taskRepository->findBy(['id' => $taskIdsArray]);
    }

    public function getUsersByTask(array $tasks): array
    {
        $usersByTask = [];
        foreach ($tasks as $task) {
            $usersByTask[] = [
                'id_task' => $task->getId(),
                'users' => $this->taskOwnerRepository->findUsersByTaskId($task->getId()),
            ];
        }

        return $usersByTask;
    }
}
