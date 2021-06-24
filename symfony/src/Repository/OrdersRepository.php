<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    /**
     * @return array|int|string
     */
    public function getAllOrders()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $id
     * @return array|int|string
     */
    public function getOrderById($id)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param string $status
     * @return mixed
     */
    public function orderFilteredByStatus(string $status)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :val')
            ->setParameter('val', $status)
            ->getQuery()
            ->getArrayResult()
            ;
    }
}
