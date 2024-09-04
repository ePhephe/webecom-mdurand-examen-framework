<?php

namespace App\Repository;

use App\Entity\Fonction;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Fonction>
 */
class FonctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fonction::class);
    }

    /**
     * Retour la liste des affectations pour une fonction
     * @return Fonction[] Returns an array of Fonction objects
     */
    public function findAffectations($value): array
    {
        return $this->createQueryBuilder('f')
            ->join('App\Entity\Affectation', 'a')
            ->andWhere('a.fonction = :fonction')
            ->setParameter('fonction', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}
