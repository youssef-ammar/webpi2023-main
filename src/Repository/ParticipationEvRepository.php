<?php

namespace App\Repository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ParticipationEv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParticipationEv>
 *
 * @method ParticipationEv|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipationEv|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipationEv[]    findAll()
 * @method ParticipationEv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationEvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipationEv::class);
    }

    public function save(ParticipationEv $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ParticipationEv $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
   

    public function chechParticipant(int $idEv, int $idUser): bool
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->select('p')
            ->where('p.idEvent = :idEv')
            ->andWhere('p.idUser = :idUser')
            ->setParameter('idEv', $idEv)
            ->setParameter('idUser', $idUser);
        $result = $qb->getQuery()->getResult();
        return !empty($result);
    }

    //    /**
    //     * @return ParticipationEv[] Returns an array of ParticipationEv objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ParticipationEv
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
