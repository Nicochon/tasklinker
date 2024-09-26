<?php

namespace App\Repository;

use App\Entity\ProjectUser;
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
}
