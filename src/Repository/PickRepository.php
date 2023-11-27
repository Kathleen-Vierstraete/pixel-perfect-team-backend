<?php

namespace App\Repository;

use App\Entity\Pick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pick>
 *
 * @method Pick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pick[]    findAll()
 * @method Pick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pick::class);
    }

    public function findByIdPerson(int $personId)
    {
        return $this->createQueryBuilder('p')
            ->select('p','pr', 'pu')
            ->join('p.purchase', 'pu')
            ->join('p.product', 'pr')
            ->join('pu.person', 'per')
            ->where('per.id = :userId')
            ->setParameter('userId',  $personId)
            ->getQuery()
            ->getResult();
    }
    



//    /**
//     * @return Pick[] Returns an array of Pick objects
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

//    public function findOneBySomeField($value): ?Pick
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
