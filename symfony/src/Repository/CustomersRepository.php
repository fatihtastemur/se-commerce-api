<?php

namespace App\Repository;

use App\Entity\Customers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findAll()
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customers::class);
    }

    /**
     * @return array|int|string
     */
    public function getActiveCustomers()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :val')
            ->setParameter('val', 1)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $id
     * @return array|int|string
     */
    public function getCustomerById($id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCustomerRevenueById($id)
    {
        $customer = $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getArrayResult()
            ;

        return $customer['revenue'];
    }
}
