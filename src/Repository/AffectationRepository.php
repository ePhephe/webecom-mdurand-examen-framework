<?php

namespace App\Repository;

use DateTime;
use App\Entity\Fonction;
use App\Entity\Affectation;
use App\Entity\Collaborateur;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Affectation>
 */
class AffectationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affectation::class);
    }

    public function searchAffectations(array $filtres): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.fonction', 'f')
            ->leftJoin('a.restaurant', 'r')
            ->leftJoin('a.collaborateur', 'c')
            ->orderBy('a.date_debut', 'DESC');

        // Si on a un filtre de recherche
        if(!empty($filtres["search"])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('UPPER(c.nom)', ':search'),
                    $queryBuilder->expr()->like('UPPER(c.prenom)', ':search'),
                    $queryBuilder->expr()->like('UPPER(c.email)', ':search')
                )
            )
            ->setParameter('search','%'.strtoupper($filtres["search"]).'%');
        }
        // Si on a un filtre de recherche
        if(!empty($filtres["ville"])) {
            $queryBuilder->andWhere('UPPER(r.ville) LIKE :ville')
            ->setParameter('ville','%'.strtoupper($filtres["ville"]).'%');
        }
        // Si on a un filtre de recherche
        if(!empty($filtres["fonction"])) {
            $queryBuilder->andWhere('a.fonction = :fonction')
            ->setParameter('fonction',$filtres["fonction"]->getId());
        }
        // Si on a un filtre de recherche
        if(!empty($filtres["date"])) {
            $queryBuilder->andWhere('a.date_debut >= :datestart')
            ->andWhere('a.date_debut <= :dateend')
            ->setParameter('datestart', $filtres["date"]->format('Y-m-d  00:00:00:000'))
            ->setParameter('dateend', $filtres["date"]->format('Y-m-d  23:59:59:999'));
        }
        // Filtre si on ne veut que les utilisateur non affectés
        if(!empty($filtres["dateFin"])) {
            $queryBuilder->andWhere('a.date_fin >= :datestart')
            ->andWhere('a.date_fin <= :dateend')
            ->setParameter('datestart', $filtres["dateFin"]->format('Y-m-d  00:00:00:000'))
            ->setParameter('dateend', $filtres["dateFin"]->format('Y-m-d  23:59:59:999'));
        }

        return $queryBuilder;
    }

    public function searchAffectationsCollaborateur(Collaborateur $collaborateur,Fonction $poste, mixed $dateAffectation)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.collaborateur = :collaborateurId')
            ->setParameter('collaborateurId', $collaborateur->getId())
            ->orderBy('a.date_debut', 'DESC');

        // Si on a un filtre de recherche
        if(!is_null($poste->getId())) {
            $queryBuilder->andWhere('a.fonction = :fonction')
            ->setParameter('fonction',$poste->getId());
        }
        
        // Filtre si on ne veut que les utilisateur non affectés
        if(!is_null($dateAffectation)) {
            $queryBuilder->andWhere('a.date_debut >= :datestart')
            ->andWhere('a.date_debut <= :dateend')
            ->setParameter('datestart', $dateAffectation->format('Y-m-d  00:00:00:000'))
            ->setParameter('dateend', $dateAffectation->format('Y-m-d  23:59:59:999'));
        }

        return $queryBuilder;
    }

    //    /**
    //     * @return Affectation[] Returns an array of Affectation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Affectation
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
