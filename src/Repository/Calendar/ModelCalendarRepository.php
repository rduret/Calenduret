<?php

namespace App\Repository\Calendar;

use App\Entity\Calendar\ModelCalendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModelCalendar>
 *
 * @method ModelCalendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelCalendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelCalendar[]    findAll()
 * @method ModelCalendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelCalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModelCalendar::class);
    }

    public function save(ModelCalendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ModelCalendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ModelCalendar[] Returns an array of ModelCalendar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ModelCalendar
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
