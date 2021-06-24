<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    /**
     * ProductsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    /**
     * @return array|int|string
     */
    public function getAllProducts()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $id
     * @return array|int|string
     */
    public function getProductById($id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.product_id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $id
     * @return int
     */
    public function getProductCategoryById($id): int
    {
        $product = $this->createQueryBuilder('p')
            ->andWhere('p.product_id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getArrayResult()
        ;

        if(count($product) > 0) {
            $product = array_shift($product);

            return $product['category'];
        } else {
            return 0;
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function getProductStockById($id): int
    {
        $product = $this->createQueryBuilder('p')
            ->andWhere('p.product_id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getArrayResult()
        ;

        if(count($product) > 0) {
            $product = array_shift($product);

            return intval($product['stock']);
        } else {
            return 0;
        }
    }

    /**
     * @param $id
     * @param $stock
     */
    public function setProductStockById($id, $stock)
    {
        $this->createQueryBuilder('p')
            ->update()
            ->set('p.stock', $stock)
            ->where('p.product_id = :product_id')
            ->setParameters([
                "product_id" => $id,
            ])
            ->getQuery()
            ->execute();
    }
}
