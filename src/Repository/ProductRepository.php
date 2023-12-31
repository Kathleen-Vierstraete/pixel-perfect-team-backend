<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByTags(Collection $value, int $id): array
    {
        return $this->createQueryBuilder('p')
            ->distinct()
            ->join('p.tags', 't')
            ->where('t.id in (:val)')
            ->andWhere('p.id <> :id')
            ->setParameters([
                'val' => $value,
                'id' => $id
            ])
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByTagsExcludeProducts(array $productIds): array
    {
        $productToString = implode(", ", $productIds);
        $sql = sprintf("SELECT product.*,picture.url
            FROM product
            JOIN product_tag ON product_tag.product_id = product.id
            JOIN tag ON tag.id = product_tag.tag_id
            JOIN picture ON  picture.product_id = product.id
            WHERE tag.id IN (SELECT tag.id 
                            FROM tag
                            JOIN product_tag ON product_tag.tag_id = tag.id 
                            WHERE product_tag.product_id IN (%s)) 
                            AND product.id NOT IN (%s)",$productToString,$productToString);

        $connexion = $this->getEntityManager()->getConnection();
        $result = $connexion->executeQuery($sql)->fetchAllAssociative();
        return $result;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
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

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
