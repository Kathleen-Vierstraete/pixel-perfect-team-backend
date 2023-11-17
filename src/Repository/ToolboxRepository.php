<?php

namespace App\Repository;

use App\Entity\Toolbox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Toolbox>
 *
 * @method Toolbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method Toolbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method Toolbox[]    findAll()
 * @method Toolbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ToolboxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Toolbox::class);
    }

//    /**
//     * @return Toolbox[] Returns an array of Toolbox objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Toolbox
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
