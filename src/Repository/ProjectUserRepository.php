<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectUser>
 */
class ProjectUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectUser::class);
    }

    public function findUserIdsByProjectId(int $idProject): array
    {
        $qb = $this->createQueryBuilder('pu')
            ->select('pu.idUser')
            ->where('pu.idProject = :idProject')
            ->setParameter('idProject', $idProject)
            ->getQuery();

        return $qb->getArrayResult(); // Retourne un tableau de résultats
    }

    public function findProjectsByUser(Users $user): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.idProject')
            ->where('u.idUser = :user')
            ->setParameter('user', $user)
            ->getQuery();

        return $qb->getArrayResult(); // Retourne un tableau de résultats

    }
}
