<?php

namespace App\Repository\Calendar;

use App\Entity\Calendar\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendar>
 *
 * @method Calendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendar[]    findAll()
 * @method Calendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    public function save(Calendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Calendar[] Returns an array of Calendar objects
    */
   public function getUnusedCalendars(): array
   {
        $limitOldDate =  new \DateTime();
        $limitOldDate->modify('-1 year');

        $limitUnactiveDate =  new \DateTime();
        $limitUnactiveDate->modify('-2 days');

        $qb = $this->createQueryBuilder('c');
        return $qb->where(
                $qb->expr()->orX(
                    //Si non utilisé depuis plus d'un an
                    $qb->expr()->lt('c.updatedAt', ':limitOldDate'),
                    //Ou si non activé et dispo depuis + de 48h
                    $qb->expr()->andX(
                        $qb->expr()->eq('c.isActive', ':isActive'),
                        $qb->expr()->lt('c.createdAt', ':limitUnactiveDate'),
                    )
                )
            )
            ->setParameters([
                'limitOldDate' => $limitOldDate,
                'isActive' => false,
                'limitUnactiveDate' => $limitUnactiveDate
            ])
            ->getQuery()
            ->getResult()
        ;
   }
}
