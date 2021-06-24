<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Products;
use App\Entity\Category;

class ApiController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * ApiController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $data
     * @param $statusCode
     * @param null $errorMessage
     * @return Response
     */
    protected function response($data, $statusCode, $errorMessage = null): Response
    {
        $response = new Response();

        if($statusCode == 200) {
            $response->setContent(json_encode([
                "status" => "success",
                "code" => 200,
                "message" => "OK",
                "data" => $data,
            ], JSON_PRETTY_PRINT));
        } else {
            $response->setContent(json_encode([
                "status" => "error",
                "code" => $statusCode,
                "message" => $errorMessage,
                "data" => [],
            ], JSON_PRETTY_PRINT));
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    protected function checkProductStock(int $productId, int $quantity): bool
    {
        $productStock = $this->entityManager->getRepository(Products::class)
            ->getProductStockById($productId);

        if($productStock > $quantity) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    protected function checkValidCategory(int $categoryId): bool
    {
        $category = $this->entityManager->getRepository(Category::class)
            ->getCategoryById($categoryId);

        if($category) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $productId
     * @return int|mixed
     */
    protected function getProductPrice($productId)
    {
        $product = $this->entityManager->getRepository(Products::class)
            ->getProductById($productId)[0];

        if($product) {
            $productPrice = $product['price'];
        } else {
            $productPrice = 0;
        }

        return $productPrice;
    }

    /**
     * @param int $productId
     * @param int $decreaseCount
     */
    protected function decreaseProductStock(int $productId, int $decreaseCount): void
    {
        $currentStock = $this->entityManager->getRepository(Products::class)
            ->getProductStockById($productId);

        $newStock = $currentStock - $decreaseCount;

        $this->entityManager->getRepository(Products::class)
            ->setProductStockById($productId, $newStock);
    }

    /**
     * @param int $productId
     * @param int $increaseCount
     */
    protected function increaseProductStock(int $productId, int $increaseCount): void
    {
        $currentStock = $this->entityManager->getRepository(Products::class)
            ->getProductStockById($productId);

        $newStock = intval($currentStock) + $increaseCount;

        $this->entityManager->getRepository(Products::class)
            ->setProductStockById($productId, $newStock);
    }
}