<?php

namespace App\Repository;

use App\Entity\TaskOwner;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskOwner>
 */
class TaskOwnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskOwner::class);
    }
    public function findTaskIdsByProjectId(int $idProject): array
    {
        $qb = $this->createQueryBuilder('pu')
            ->select('DISTINCT pu.idTask')
            ->where('pu.idProject = :idProject')
            ->setParameter('idProject', $idProject)
            ->getQuery();

        return $qb->getResult(); // Retourne un tableau de résultats
    }

    public function findUsersByTaskId(int $idTask): array
    {
        $qb = $this->createQueryBuilder('pu')
            ->innerJoin('App\Entity\Users', 'u', 'WITH', 'pu.idUser = u.id')
            ->select('u')
            ->where('pu.idTask = :idTask')
            ->setParameter('idTask', $idTask)
            ->getQuery();

        $results = $qb->getResult();

        return $results; // Retourne un tableau de résultats
    }

    public function findProjectIdByTaskId(int $idTask): ?int
    {
        $qb = $this->createQueryBuilder('pu')
            ->select('DISTINCT pu.idProject')
            ->where('pu.idTask = :idTask')
            ->setParameter('idTask', $idTask)
            ->getQuery();

        return $qb->getSingleScalarResult();
    }
}
