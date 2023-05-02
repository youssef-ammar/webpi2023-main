<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function save(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getSeveneVENEMENT(
        EntityManagerInterface $entityManager
    ): array {
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder
            ->select('evenement')
            ->from(Evenement::class, 'evenement')
            ->setMaxResults(7);

        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        return $results;
    }

    public function findTopThreeEvents()
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id',  'COUNT(p) AS participantCount')
            ->leftJoin('e.participations', 'p')
            ->groupBy('e.id')
            ->orderBy('participantCount', 'DESC')
            ->setMaxResults(3);

        return $qb->getQuery()->getResult();
    }

    public function findEvenementByNomEv($nomEv)
{
    return $this->createQueryBuilder('e')
        ->where('e.nom_ev LIKE :nomEv')
        ->setParameter('nomEv', '%' . $nomEv . '%')
        ->getQuery()
        ->getResult();
}
    // public function search($data): array
    // {
    //     $qb = $this->createQueryBuilder('c')
    //         ->where('c.nomEv LIKE :keyword')
    //         ->setParameter('keyword', '%' . $data['nomEv'] . '%');

    //     if ($data['emplacement']) {
    //         $qb
    //             ->andWhere('c.emplacement = :emplacement')
    //             ->setParameter('emplacement', $data['emplacement']);
    //     }
    //     if ($data['region']) {
    //         $qb
    //             ->andWhere('c.region = :region')
    //             ->setParameter('region', $data['region']);
    //     }

    //     return $qb->getQuery()->getResult();
    // }

    //    /**
    //     * @return Evenement[] Returns an array of Evenement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evenement
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
