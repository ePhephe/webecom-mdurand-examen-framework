<?php

namespace App\Repository;

use App\Entity\Collaborateur;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<Collaborateur>
 */
class CollaborateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaborateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Collaborateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Recherche de collaborateurs
     *
     * @param string $search Texte de la recherche
     * @param boolean $noAffectation Filtre sur les collaborateurs sans affectation en cours
     * @return QueryBuilder
     */
    public function searchCollaborateurs(mixed $search, bool $noAffectation): QueryBuilder
    {
        // Création du QueryBuilder
        $queryBuilder = $this->createQueryBuilder('c')->orderBy('c.id','DESC');

        // Si on a un filtre de recherche
        if(!empty($search)) {
            $paramSearch = ":search";
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('UPPER(c.nom)',  $paramSearch),
                    $queryBuilder->expr()->like('UPPER(c.prenom)',  $paramSearch),
                    $queryBuilder->expr()->like('UPPER(c.email)',  $paramSearch)
                )
            )
            ->setParameter($paramSearch,'%'.strtoupper($search).'%');
        }

        // Filtre si on ne veut que les utilisateur non affectés
        if($noAffectation) {
            // On créé une requête imbriqué qui récupère les affectations
            $subQuery = $this->getEntityManager()->createQueryBuilder()
                ->select('a')
                ->from('App\Entity\Affectation', 'a')
                ->where('a.collaborateur = c.id')
                ->andWhere('a.date_fin IS NULL OR a.date_fin > :now');
            $queryBuilder->where($queryBuilder->expr()->not($queryBuilder->expr()->exists($subQuery->getDQL())))
            ->setParameter('now', new \DateTime());
        }

        return $queryBuilder;
    }

    //    /**
    //     * @return Collaborateur[] Returns an array of Collaborateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Collaborateur
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
