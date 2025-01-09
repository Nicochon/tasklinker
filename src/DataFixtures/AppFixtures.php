<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\TaskOwner;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new Users();
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setMail($faker->email);
            $user->setContract('CDI');
            $user->setStartDateContract($faker->dateTimeBetween('-2 years', 'now'));
            $user->setRoles(['ROLE_USER']);

            $plainPassword = 'password' . $i;
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();

        $projects = [];
        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName($faker->sentence(3));

            $manager->persist($project);
            $projects[] = $project;
        }

        $manager->flush();

        $tasks = [];
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setName($faker->sentence(4));
            $task->setStartDate($faker->dateTimeBetween('-1 month', 'now'));
            $task->setEndDate($faker->dateTimeBetween('now', '+1 month'));
            $task->setDescription($faker->paragraph);
            $task->setStatus($faker->randomElement(['To Do', 'Doing', 'Done']));

            $manager->persist($task);
            $tasks[] = $task;
        }

        $manager->flush();

        foreach ($tasks as $task) {
            if (!empty($users) && !empty($projects)) {
                $taskOwner = new TaskOwner();
                $taskOwner->setIdTask($task->getId());
                $taskOwner->setIdProject($projects[array_rand($projects)]->getId());
                $taskOwner->setIdUser($users[array_rand($users)]->getId());

                $manager->persist($taskOwner);
            }
        }

        // Enregistrer tout
        $manager->flush();
    }
}
