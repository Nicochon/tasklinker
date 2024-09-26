<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\Task;
use App\Entity\TaskOwner;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de 10 utilisateurs fictifs
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = new Users();
            $user->setFirstName('FirstName ' . $i);
            $user->setLastName('LastName ' . $i);
            $user->setMail('user' . $i . '@example.com');
            $user->setContract('Contract ' . $i);
            $user->setStartDateContract(new \DateTime('now'));

            // Persister l'utilisateur
            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();

        $projects = [];
        for ($j = 1; $j <= 2; $j++) {
            $project = new Project();
            $project->setName('Projet Test ' . $j);
            $manager->persist($project);
            $projects[] = $project;
        }

        $manager->flush();

        if (count($users) > 0 && count($projects) > 0) {
            $projectUser = new ProjectUser();
            $projectUser->setIdUser($users[3]->getId());
            $projectUser->setIdProject($projects[0]->getId());
            $manager->persist($projectUser);
        }

        $manager->flush();

        $tasks = [];
        for ($k = 1; $k <= 5; $k++) {
            $taskObject = new Task();
            $taskObject->setName('Tâche ' . $k);
            $taskObject->setStartDate(new \DateTime('now'));
            $taskObject->setEndDate(new \DateTime('+1 week'));
            $taskObject->setDescription('Description de la tâche ' . $k);
            $taskObject->setStatus('To Do'); // Statut de la tâche

            $manager->persist($taskObject);
            $tasks[] = $taskObject;
        }
        $manager->flush();

        foreach ($tasks as $task) {
            if (!empty($users) && !empty($projects)) {
                $taskOwner = new TaskOwner();
                $taskOwner->setIdTask($task->getId());
                $taskOwner->setIdProject($projects[array_rand($projects)]->getId());
                $taskOwner->setIdUser($users[array_rand($users)]->getId());

                // Persister la liaison entre la tâche, le projet, et l'utilisateur
                $manager->persist($taskOwner);
            }
        }

        // Enregistrer toutes les tâches dans la base de données
        $manager->flush();
    }
}
