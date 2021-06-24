<?php

namespace App\Controller;

use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Products;
use App\Helper\ApiHelper;
use Throwable;

class ProductController extends ApiController
{
    /**
     * @Route("/api/v1/products", methods={"GET"})
     */
    public function getAllProducts(): Response
    {
        $products = $this->entityManager->getRepository(Products::class)
            ->getAllProducts();

        $result = [];
        foreach ($products as $product) {
            $result[] = array(
                'id' => $product['product_id'],
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => ApiHelper::numberFormater($product['price']),
                'stock' => $product['stock']
            );
        }

        return $this->response($result, 200, null);
    }

    /**
     * @Route("/api/v1/product/{id}", methods={"GET"})
     */
    public function getProduct(int $id): Response
    {
        $product = $this->entityManager->getRepository(Products::class)
            ->getProductById($id);

        if($product) {
            $product[0]['price'] = ApiHelper::numberFormater($product[0]['price']);
            unset($product[0]['id']);

            $response = $this->response(array_shift($product), 200, null);
        } else{
            $response = $this->response([], 404, "Product Not Found!");
        }

        return $response;
    }

    /**
     * @Route("/api/v1/product/create", methods={"PUT"})
     */
    public function createProduct(Request $request): Response
    {
        try{
            $parameters = json_decode($request->getContent(), true);
            $productId = $parameters['product_id'];
            $name = $parameters['name'];
            $category = $parameters['category'];
            $price = $parameters['price'];
            $stock = $parameters['stock'];

            if($productId && $name && $category && $price && $stock) {

                if($category <= 0) {
                    return $this->response([], 400, "Invalid Category!");
                } else {
                    $checkCategory = $this->checkValidCategory($category);
                    if(!$checkCategory) {
                        return $this->response([], 404, "Category Not Found!");
                    }
                }

                if($stock <= 0) {
                    return $this->response([], 400, "Invalid Stock!");
                }

                $product = new Products();
                $product->setProductId($productId);
                $product->setName($name);
                $product->setCategory($category);
                $product->setPrice($price);
                $product->setStock($stock);

                $this->entityManager->persist($product);
                $this->entityManager->flush();

                $createdId = $product->getProductId();

                $response = $this->response(["created_product_id" => $createdId], 200, null);

            } else {
                $response = $this->response([], 400, "Missing Parameter!");
            }
        } catch (Throwable $exception) {
            $response = $this->response([], $exception->getCode(), $exception->getMessage());
        }

        return $response;
    }

    /**
     * @Route("/api/v1/product/update", methods={"POST"})
     */
    public function updateProduct(Request $request): Response
    {
        $id = $request->query->get('id');
        $price = $request->query->get('price');
        $stock = $request->query->get('stock');

        if($id && $id > 0) {

            $product = $this->entityManager->getRepository(Products::class)
                ->findOneBy(['product_id' => $id]);

            if ($product) {
                if ($price && $price > 0) {
                    $product->setPrice($price);
                }

                if ($stock) {
                    $product->setStock($stock);
                }

                $this->entityManager->flush();
                $response = $this->response(["updated_product_id" => $id], 200, null);
            } else {
                $response = $this->response([], 404, "Product Not Found!");
            }
        } else {
            $response = $this->response([], 404, "Incorrect Product ID!");
        }

        return $response;
    }

    /**
     * @Route("/api/v1/product/{id}", methods={"DELETE"})
     */
    public function deleteProduct(int $id): Response
    {
        $product = $this->entityManager->getRepository(Products::class)
            ->findOneBy(['product_id' => $id]);

        if($product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();

            $response = $this->response(["deleted_product_id" => $id], 200, null);
        } else {
            $response = $this->response([], 404, "Product Not Found!");
        }

        return $response;
    }
}
