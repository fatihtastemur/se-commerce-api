<?php

namespace App\Controller;

use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Orders;
use App\Entity\OrderProducts;
use App\Entity\Products;
use App\Helper\ApiHelper;
use App\Constants\GeneralConstants;
use Throwable;

class OrderController extends ApiController
{
    /**
     * @Route("/api/v1/orders", methods={"GET"})
     */
    public function getAllOrders(): Response
    {
        $orders = $this->entityManager->getRepository(Orders::class)
            ->getAllOrders();

        $result = [];
        foreach ($orders as $order) {
            $result[] = array(
                'id' => $order['id'],
                'customerId' => $order['customer_id'],
                'items' => $this->getOrderProducts($order['id']),
                'total' => ApiHelper::numberFormater($order['total_price'])
            );
        }

        return $this->response($result, 200, null);
    }

    /**
     * @Route("/api/v1/order/filter", methods={"GET"})
     */
    public function getOrdersByStatus(Request $request): Response
    {
        $status = $request->query->get('status');

        $orders = $this->entityManager->getRepository(Orders::class)
            ->orderFilteredByStatus($status);

        $result = [];
        foreach ($orders as $order) {
            $result[] = array(
                'id' => $order['id'],
                'customerId' => $order['customer_id'],
                'items' => $this->getOrderProducts($order['id']),
                'total' => ApiHelper::numberFormater($order['total_price'])
            );
        }

        return $this->response($result, 200, null);
    }

    /**
     * @Route("/api/v1/order/{id}", methods={"GET"})
     */
    public function getOrder(int $id): Response
    {
        $order = $this->entityManager->getRepository(Orders::class)
            ->getOrderById($id);

        if($order) {
            $result = array(
                'id' => $order[0]['id'],
                'customerId' => $order[0]['customer_id'],
                'items' => $this->getOrderProducts($order[0]['id']),
                'total' => ApiHelper::numberFormater($order[0]['total_price'])
            );

            $response = $this->response($result, 200, null);
        } else{
            $response = $this->response([], 404, "Order Not Found!");
        }

        return $response;
    }

    /**
     * @Route("/api/v1/order/create", methods={"PUT"})
     */
    public function createOrder(Request $request): Response
    {
        try{
            $parameters = json_decode($request->getContent(), true);
            $customerId = $parameters['customer_id'];
            $products = $parameters['products'];

            $orderTotalPrice = 0;
            foreach ($products as $product) {
                $checkProductStock = $this->checkProductStock($product['product_id'], $product['quantity']);
                if(!$checkProductStock) {
                    $errorMessage = "Insufficient Stock of Products(ID:" . $product['product_id'] . ")!";
                    return $this->response([], 400, $errorMessage);
                }

                $productPrice = $this->getProductPrice($product['product_id']);
                $orderTotalPrice += ($productPrice * $product['quantity']);
            }

            $now = new \DateTime();

            $order = new Orders();
            $order->setCustomerId($customerId);
            $order->setStatus(GeneralConstants::ORDER_STATUS_PENDING);
            $order->setTotalPrice($orderTotalPrice);
            $order->setCreatedAt($now);

            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $createdId = $order->getId();

            $this->setOrderProducts($createdId, $products);

            $response = $this->response(["created_order_id" => $createdId], 200, null);

        } catch (Throwable $exception) {
            $response = $this->response([], $exception->getCode(), $exception->getMessage());
        }

        return $response;
    }

    /**
     * @Route("/api/v1/order/{id}", methods={"DELETE"})
     */
    public function deleteOrder(int $id): Response
    {
        $order = $this->entityManager->getRepository(Orders::class)->findOneBy(['id' => $id]);

        if($order) {
            $this->entityManager->remove($order);
            $this->entityManager->flush();

            $this->deleteOrderProducts($id);

            $response = $this->response(["deleted_order_id" => $id], 200, null);
        } else {
            $response = $this->response([], 404, "Order Not Found!");
        }

        return $response;
    }

    /**
     * @param $orderId
     * @return array
     */
    public function getOrderProducts($orderId): array
    {
        $orderProducts = $this->entityManager->getRepository(OrderProducts::class)
            ->getOrderProductsByOrderId($orderId);

        $result = [];
        foreach ($orderProducts as $orderProduct) {
            $result[] = array(
                'productId' => $orderProduct['product_id'],
                'quantity' => $orderProduct['quantity'],
                'unitPrice' => ApiHelper::numberFormater($orderProduct['unit_price']),
                'total' => ApiHelper::numberFormater($orderProduct['total_price'])
            );
        }

        return $result;
    }

    /**
     * @param int $orderId
     * @param array $products
     */
    public function setOrderProducts(int $orderId, array $products): void
    {
        foreach ($products as $product) {

            $unitPrice = $this->getProductPrice($product['product_id']);
            $totalPrice = ($unitPrice * $product['quantity']);

            $orderProduct = new OrderProducts();
            $orderProduct->setOrderId($orderId);
            $orderProduct->setProductId($product['product_id']);
            $orderProduct->setQuantity($product['quantity']);
            $orderProduct->setUnitPrice($unitPrice);
            $orderProduct->setTotalPrice($totalPrice);

            $this->entityManager->persist($orderProduct);
            $this->entityManager->flush();

            if($orderProduct->getId() > 0) {
                #update product stock
                $this->decreaseProductStock($product['product_id'], $product['quantity']);
            }
        }
    }

    /**
     * @param $orderId
     */
    public function deleteOrderProducts($orderId): void
    {
        $orderProducts = $this->entityManager->getRepository(OrderProducts::class)
            ->getOrderProductsByOrderId($orderId);

        foreach ($orderProducts as $orderProduct) {
            $item = $this->entityManager->getRepository(OrderProducts::class)
                ->findOneBy(
                    [
                        'order_id' => $orderId,
                        'product_id' => $orderProduct['product_id'],
                    ]
                );

            if($item) {
                #update product stock
                $itemQuantity = $item->getQuantity();
                $this->increaseProductStock($orderProduct['product_id'], $itemQuantity);

                $this->entityManager->remove($item);
                $this->entityManager->flush();
            }
        }
    }
}
