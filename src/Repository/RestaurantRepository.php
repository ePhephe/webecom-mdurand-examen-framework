<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Restaurant;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Restaurant>
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function searchAffectations (
        $restaurant,
        $search,
        $fonctionId,
        $dateDebutAffectation,
        EntityManagerInterface $entityManager,
        $historique = false
    ) : QueryBuilder
    {
        // Création du QueryBuilder
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('a', 'f', 'c')
        ->from('App\Entity\Affectation', 'a')
        ->join('a.fonction', 'f')
        ->join('a.restaurant', 'r')
        ->join('a.collaborateur', 'c')
        ->where('r.id = :restaurantId')
        
        ->setParameter('restaurantId', $restaurant->getId());

        // Si on ne veut pas tout l'historique
        if(!$historique) {
            $queryBuilder->andWhere('a.date_fin IS NULL');
        }

        // Paramètres de recherche
        if (!empty($search)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('UPPER(c.nom)', ':search'),
                    $queryBuilder->expr()->like('UPPER(c.prenom)', ':search'),
                    $queryBuilder->expr()->like('UPPER(c.email)', ':search')
                )
            )
                ->setParameter(':search', '%' . strtoupper($search) . '%');
        }
        if (!empty($fonctionId)) {
            $queryBuilder->andWhere('f.id = :fonctionId')
            ->setParameter('fonctionId', $fonctionId);
        }
        if (!empty($dateDebutAffectation)) {
            $queryBuilder->andWhere('a.date_debut = :dateDebutAffectation')
            ->setParameter('dateDebutAffectation', $dateDebutAffectation);
        }
    
        return $queryBuilder;
    }

    //    /**
    //     * @return Restaurant[] Returns an array of Restaurant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restaurant
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
