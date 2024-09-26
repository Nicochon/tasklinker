<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Users>
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function remove(Users $user, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($user);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
