<?php

namespace App\Repository;

use App\Entity\Products;
use App\Entity\OrderProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProducts[]    findAll()
 * @method OrderProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProducts::class);
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getOrderProductsByOrderId($orderId)
    {
        return $this->createQueryBuilder('op')
            ->andWhere('op.order_id = :val')
            ->setParameter('val', $orderId)
            ->orderBy('op.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $orderId
     * @param $category
     * @return mixed
     */
    public function getOrderProductsByOrderAndCategoryId($orderId, $category) 
    {
        return $this->createQueryBuilder('op')
            ->select('op')
            ->innerJoin(Products::class,'p','WITH','p.product_id=op.product_id')
            ->andWhere('op.order_id = :val')
            ->setParameter('val', $orderId)
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('op.unit_price', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }
}
