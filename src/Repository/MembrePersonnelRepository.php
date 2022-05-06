<?php

namespace App\Repository;

use App\Entity\MembrePersonnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembrePersonnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembrePersonnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembrePersonnel[]    findAll()
 * @method MembrePersonnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembrePersonnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembrePersonnel::class);
    }

    // /**
    //  * @return MembrePersonnel[] Returns an array of MembrePersonnel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MembrePersonnel
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalMembrePersonnel(){
        $query = $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ;
            return $query->getQuery()->getSingleScalarResult();
    }
}
